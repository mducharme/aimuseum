<template>
  <div class="loading-screen">
    <div class="spinner"></div>
    <div class="message">Loading...</div>
  </div>
</template>

<style>
.loading-screen {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: white;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.spinner {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  border: 5px solid #ccc;
  border-top-color: #333;
  animation: spin 1s linear infinite;
}

.message {
  margin-top: 10px;
  font-size: 20px;
  color: #333;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>