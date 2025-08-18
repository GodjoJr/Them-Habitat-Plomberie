<template>
    <div class="sidebar">
        <ul>
            <li><RouterLink :to="{ name: 'settings'}">Options</RouterLink></li>
            <li><RouterLink :to="{ name: 'templates'}">Thèmes</RouterLink></li>
            <li><RouterLink :to="{ name: 'plugins'}">Extensions</RouterLink></li>
        </ul>
    </div>
</template>

<style lang="scss" scoped>
.sidebar{
  width: 100%;
  height: 100%;
  position: absolute;
  left: 0;
  top: 0;

  ul{
    padding: 20px;
    margin: 0;

    li{
      a{
        text-decoration: none;
      }
    }
  }
}
</style>
<script setup>
</script>