<script setup>
import Card from '../components/Plugins/Card.vue'
</script>

<template>
    <div class="coqpit-core-plugins">
        <div class="list">
            <Card/>
            <Card/>
            <Card/>
            <Card/>
            <Card/>
            <Card/>
            <Card/>
            <Card/>
            <Card/>
            <Card/>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.coqpit-core-plugins{
  padding: 20px;

  .list{
    display: grid;
    max-width: 1300px;
    grid-gap: 20px;
    grid-template-columns: repeat(4, 1fr);
  }
}
</style>