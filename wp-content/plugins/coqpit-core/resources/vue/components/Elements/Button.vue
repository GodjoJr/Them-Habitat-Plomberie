<script setup>
import { computed } from 'vue'

const props = defineProps({
  icon: Boolean,
  danger: Boolean,
  align: String
})

const alignment = computed(() => {
  if(!props.align) return false;
  return (props.align === 'left') ? 'align-left' : 'align-right';
})
</script>

<template>
    <div class="button" :class="[alignment, { 'icon-only': icon, 'is-danger': danger }]">
        <slot name="icon"></slot>
        <slot name="default"></slot>
    </div>
</template>

<style lang="scss" scoped>
.button{
  display: inline-block;
  width: auto;
  min-width: 36px;
  height: 36px;
  background-color: white;
  border-radius: 5px;
  padding: 0 20px 0 7px;
  line-height: 36px;
  cursor: pointer;
  border: 1px solid darken(white, 20%);
  font-size: 13px;
  font-weight: 500;
  box-sizing: border-box;
  color: $black;
  transition: color .2s linear, background-color .2s linear, border .2s linear;
  box-shadow: 0 1px 3px rgba($black, .1);
  text-align: center;

  &:hover{
    color: darken($yellow, 45%);
    background-color: $yellow;
    border: 1px solid darken($yellow, 20%);

    :deep(svg){
      fill: darken($yellow, 35%);
    }
  }

  :deep(svg){
    fill: rgba($black, .7);
    margin: 0 8px;
    transition: fill .2s linear;
  }

  &.icon-only{
    padding: 0;

    &:hover{
      :deep(svg){
        fill: darken($yellow, 45%);
      }
    }

    :deep(svg){
      fill: $black;
      margin: 0;
    }
  }

  &.is-danger{
    color: $danger;
    border: 1px solid lighten($danger, 30%);
    box-shadow: 0 1px 3px rgba(darken($danger, 10%), .1);

    &:hover{
      color: white;
      border: 1px solid $danger;
      background-color: $danger;

      :deep(svg){
        fill: white;
      }
    }

    :deep(svg){
      fill: $danger;
    }
  }

  &.align-right{
    float: right;
    margin-left: 10px;
  }

  &.align-left{
    float: left;
    margin-right: 10px;
  }
}
</style>