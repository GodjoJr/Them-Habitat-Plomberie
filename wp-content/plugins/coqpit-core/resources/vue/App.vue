<script setup>
import Sidebar from './components/Sidebar.vue'
</script>

<template>
    <div class="app">
        <div class="sidebar-container">
            <Sidebar/>
        </div>
        <div class="body-container">
            <div class="body-inner">
                <RouterView/>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.app{
  display: grid;
  grid-template-columns: 250px 1fr;
  min-height: calc(100vh - 32px);
  width: 100%;

  .sidebar-container{
    position: relative;
    min-height: calc(100vh - 32px);
    background-color: $background;
  }

  .body-container{
    padding: 20px 20px 20px 0;
    box-sizing: border-box;

    .body-inner{
      background-color: white;
      box-sizing: border-box;
      border-radius: 10px;
      min-height: 100%;
    }
  }
}
</style>