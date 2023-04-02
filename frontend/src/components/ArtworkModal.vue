<template>
  <div class="modal-container">
    <div class="modal-content" role="dialog" aria-labelledby="artwork-title" @click.stop>
      <button class="close-btn" @click.stop="closeModal">&times;</button>
      <div class="image-container">
        <img class="artwork-image" :src="fullImageUrl" alt="Artwork Image ({ artwork.title }})">
      </div>
      <div class="artwork-info">
        <h2 class="artwork-title" id="artwork-title">{{ artwork.title }}</h2>
        <p class="artwork-description">{{ artwork.description }}</p>
        <div class="artwork-colors">
          <div class="color-box" v-for="color in artwork.palette" :key="color.hex" :style="{ backgroundColor: color.hex }" :data-tooltip="color.name"></div>
        </div>
        <ul class="artwork-tags">
          <li v-for="(tag, index) in artwork.tags" :key="index">{{ tag }}</li>
        </ul>
        <p class="artwork-created-at">Date: {{ formattedDate }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    artwork: {
      type: Object,
      required: true
    },
    modalOpen: {
      type: Boolean,
      required: true
    }
  },
  computed: {
    formattedDate() {
      const date = new Date(this.artwork.created_at);
      return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric'
      }).format(date);
    },
    fullImageUrl() {
      return 'http://localhost:9090/textures/' + this.artwork.id + '.png';
    }
  },
  methods: {
    closeModal() {
      console.log('close');
      this.$emit('close-modal');
      //this.modalOpen = false;
    }
  },
  mounted() {
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        this.closeModal();
      }
    });
  },
  beforeUnmount() {
    document.removeEventListener('keydown', this.closeModal);
  }
};
</script>

<style scoped>

.modal-container {
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.8);

}

.modal-content {
  background-color: #fefefe;
  margin: 5% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 90%;
  max-width: 700px;
  position: relative;

  display: flex;
  flex-direction: column;
  align-items: center;
}

@media (min-width: 768px) {
  .modal-content {
    width: 80%;
  }
}

@media (min-width: 992px) {
  .modal-content {
    width: 60%;
  }
}

@media (min-width: 1200px) {
  .modal-content {
    width: 50%;
  }
}

.close-btn {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    position: absolute;
    right: 10px;
    top: 0;
}

.close-btn:hover,
.close-btn:focus {
    color: black;
    cursor: pointer;
}

.image-container {
  width: 100%;
  max-width: 512px;
  max-height: 512px;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
  margin-bottom: 20px;
}

.artwork-image {
  width: auto;
  height: auto;
  max-width: 100%;
  max-height: 100%;
}

.artwork-title {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 10px;
}

.artwork-description {
    margin-bottom: 20px;
}

.artwork-colors{
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    margin-bottom: 20px;
}

.color-box {
    width: 50px;
    height: 50px;
    margin-right: 10px;
    border: 1px solid #ddd;
}

.color-box:last-child {
    margin-right: 0;
}

.color-box:hover {
    cursor: pointer;
    opacity: 0.8;
}

.color-box::after {
    content: attr(data-tooltip);
    display: none;
    position: relative;
    top: -30%;
    left: 50%;
  margin-bottom:30px;
    transform: translate(-50%, -50%);
    padding: 5px 10px;
    font-size: 14px;
    background-color: #333;
    color: #fff;
    border-radius: 3px;
    white-space: nowrap;
}

.color-box:hover::after {
    display: inline-block;
}

.artwork-tags {
    list-style-type: none;
    margin: 0;
    padding: 0;
}

.artwork-tags li {
    display: inline-block;
    margin-right: 10px;
    margin-bottom: 10px;
    background-color: #eee;
    padding: 5px 10px;
    border-radius: 3px;
    font-size: 14px;
}
</style>