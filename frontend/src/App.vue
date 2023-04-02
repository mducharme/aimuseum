<template>
  <loading-screen :isLoading="isLoading" v-if="isLoading"/>
  <modal-popup @close-modal="closeModal" :artwork="artwork" :modalOpen="modalOpen"  v-if="modalOpen"/>
  <div id="#app" style="margin:0;">
  </div>
</template>

<script>
import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import ArtworkModal from './components/ArtworkModal.vue';
import LoadingScreen from './components/LoadingScreen.vue';

export default {
  name: 'App',
  el: "#app",
  components: {
    ModalPopup: ArtworkModal,
    LoadingScreen: LoadingScreen
  },
  data() {
    return {
      isLoading: true,
      modalOpen: false,
      artwork: {},
      textures: {}
    };
  },
  methods: {
    closeModal() {
      this.modalOpen = false;
    }
  },
  mounted() {
    const vm = this;

    (async () => {
      await setTimeout(()=>vm.isLoading=false, 1000)
    })()



    // Settings
    const numCubes = 8;//Math.floor( Math.random() * (25 - 10) ) + 10;
    console.log(numCubes + "cubes");
    const cubeSize = 50;
    const rotationSpeed = { x: 0.01, y: 0.01, z: 0.0 };
    //const initialTexturePath = "/default.png";

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera( 75, window.innerWidth / window.innerHeight, 0.1, 1000 );
    const renderer = new THREE.WebGLRenderer();
    renderer.setSize( window.innerWidth, window.innerHeight );
    document.body.appendChild( renderer.domElement );

    const cubes = createCubes(numCubes, cubeSize);
    console.log(cubes.length+"cubes");
    cubes.forEach(cube => scene.add(cube));

    const controls = getOrbitControls( camera, renderer );


    const initialZoom = getInitialZoom(camera, scene);
    camera.position.set( 0, 0, initialZoom+cubeSize );

    loadTextureList('http://localhost:9090/api/texture_list.php', (files) => {
      // Set up texture loader and texture folder path
      const textureLoader = new THREE.TextureLoader();
      const textureFolderPath = "http://localhost:9090/textures/"; // change this to your texture folder path

      // Create textures array by mapping the loaded filenames to their full paths
      /*const*/ this.textures = files.map((filename) => textureFolderPath + filename);

      cubes.forEach(cube => {
        cube.material.forEach(material => {
          changeTextureWithRandomInterval(material);
        });
      });


      // Function to change texture for a given material after a random interval
      function changeTextureWithRandomInterval(material) {
        const textureChangeIntervalMin = 10000; // Minimum interval (in milliseconds)
        const textureChangeIntervalMax = 30000; // Maximum interval (in milliseconds)
        const randomInterval = Math.random() * (textureChangeIntervalMax - textureChangeIntervalMin) + textureChangeIntervalMin;

        setTimeout(() => {
          // Randomly select a new texture for the material
          const randomTexture = vm.textures[Math.floor(Math.random() * vm.textures.length)];
          textureLoader.load(randomTexture, (texture) => {
            material.map = texture;
            material.needsUpdate = true;
          });

          // Hide the loading screen
          //this.isLoading = false;

          // Schedule the next texture change
          changeTextureWithRandomInterval(material);
        }, randomInterval);
      }


    });

// Start the animation loop
    animate(rotationSpeed);



// Function to detect the clicked cube face and show the popup
    function onCubeFaceClick(event) {
      event.preventDefault();

      const mouse = new THREE.Vector2();
      const raycaster = new THREE.Raycaster();

      mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
      mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

      raycaster.setFromCamera(mouse, camera);

      const intersects = raycaster.intersectObjects(cubes);

      if (intersects.length > 0) {
        const intersection = intersects[0];
        const clickedCube = intersection.object;
        const clickedFace = intersection.face;
        const materialIndex = clickedFace.materialIndex;
        //const textureFilename = this.textures[materialIndex];
        const material = clickedCube.material[materialIndex];
        const source = material.map.source;
        const image = source.data;
        const textureFilename = image.currentSrc;
        //console.log(materialIndex);

        // Pause texture change
        //clearTimeout(clickedCube.material[materialIndex].timeoutId);

        fetchArtworkInfo(textureFilename);
      }
    }

// Function to handle window resizing events
    function onWindowResize() {
      // Update the camera's aspect ratio
      camera.aspect = window.innerWidth / window.innerHeight;

      // Update the camera's projection matrix
      camera.updateProjectionMatrix();

      // Update the renderer's size
      renderer.setSize(window.innerWidth, window.innerHeight);
    }

    function fetchArtworkInfo(textureFilename) {
      // Replace this URL with the actual API endpoint that provides artwork information

      const match = textureFilename.match( /[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i);
      const id = match[0];

      fetch("http://localhost:9090/api/artwork_info.php?id="+id)
          .then((response) => response.json())
          .then(data => {
            console.log(data);
            vm.artwork = data;
            vm.modalOpen = true;
          })
          .catch((error) => {
            console.error("Error fetching artwork info:", error);
          });
    }

// Add an event listener for mouse clicks
    window.addEventListener('click', onCubeFaceClick, false);

// Add a resize event listener to the window
    window.addEventListener('resize', onWindowResize, false);

// Event listener to change rotation speed on mouse move
    window.addEventListener(
        'mousemove',
        (event) => {
          const mouseX = event.clientX / window.innerWidth;
          const mouseY = event.clientY / window.innerHeight;

          rotationSpeed.x = (mouseY - 0.5) * 0.018 + 0.01;
          rotationSpeed.y = (mouseX - 0.5) * 0.018 + 0.01;
        },
        false
    );

    function getInitialZoom(camera, scene) {
      // Create a bounding box for the scene
      const boundingBox = new THREE.Box3().setFromObject(scene);

      // Calculate the size of the bounding box
      const boundingBoxSize = new THREE.Vector3();
      boundingBox.getSize(boundingBoxSize);

      // Find the maximum dimension of the bounding box
      const maxDimension = Math.max(
          boundingBoxSize.x,
          boundingBoxSize.y,
          boundingBoxSize.z
      );

      // Calculate the initial zoom level based on the maximum dimension
      // and the camera's field of view
      return maxDimension / (2 * Math.tan(Math.PI * camera.fov / 360));

    }

    function createCubes(numCubes, cubeSize) {
      const cubes = [];
      const occupiedPositions = [];

      for (let i = 0; i < numCubes; i++) {
        const geometry = new THREE.BoxGeometry(cubeSize, cubeSize, cubeSize);
        const cubeMaterials = Array(6).fill().map(() => new THREE.MeshBasicMaterial({ map: null }));
        const cubeMesh = new THREE.Mesh(geometry, cubeMaterials);

        let positionIsValid = false;
        let numTries = 0;

        while (!positionIsValid && numTries < 300) {
          const position = new THREE.Vector3(
              Math.random() * cubeSize * 2.8 - cubeSize * 1.4,
              Math.random() * cubeSize * 2.8 - cubeSize * 1.4,
              Math.random() * cubeSize * 2.8 - cubeSize * 1.4
          );

          positionIsValid = true;

          for (const occupiedPosition of occupiedPositions) {
            if (position.distanceTo(occupiedPosition) < cubeSize * 1.5) { // Increase the minimum distance between cubes
              positionIsValid = false;
              break;
            }
          }

          if (positionIsValid) {
            cubeMesh.position.copy(position);
            occupiedPositions.push(position.clone());
          } else {
            numTries++;
          }
        }

        cubes.push(cubeMesh);
      }

      return cubes;
    }

    function getOrbitControls(camera, renderer) {
      const controls = new OrbitControls(camera, renderer.domElement);

      // Enable damping for smoother camera movement
      controls.enableDamping = true;
      controls.dampingFactor = 0.05;

      // Disable panning in screen space
      controls.screenSpacePanning = false;

      // Set minimum and maximum distance for zooming
      controls.minDistance = 40;
      controls.maxDistance = 350;

      // Restrict the vertical rotation angle
      controls.maxPolarAngle = Math.PI / 2;

      return controls;
    }

    function loadTextureList(url, callback) {
      fetch(url)
          .then((response) => response.json())
          .then((files) => {
            callback(files);
          })
          .catch((error) => {
            console.error('Error loading texture list:', error);
          });
    }

    function animate(rotationSpeed) {
      // Schedule the next call to the animate function when the browser is ready
      // to repaint the screen, creating a smooth animation loop.
      requestAnimationFrame(() => animate(rotationSpeed));

      // Rotate each cube by the specified amount around each axis (x, y, z).
      cubes.forEach(cube => {
        cube.rotation.x += rotationSpeed.x;
        cube.rotation.y += rotationSpeed.y;
        cube.rotation.z += rotationSpeed.z;
      });

      // Render the scene using the specified camera.
      renderer.render(scene, camera);

      // Update the camera controls (OrbitControls) based on user input,
      // such as mouse or touch events.
      controls.update();
    }

  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}
</style>
