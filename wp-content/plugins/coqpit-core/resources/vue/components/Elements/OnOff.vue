<script setup>
import IconCheck from '../Icons/IconCheck.vue'
import IconCross from '../Icons/IconCross.vue'

import { computed, defineEmits } from 'vue'

const emits = defineEmits(['update:modelValue'])

const props = defineProps({
  modelValue: Boolean,
  align: String
})

const alignment = computed(() => {
  if(!props.align) return false;
  return (props.align === 'left') ? 'align-left' : 'align-right';
})
</script>

<template>
    <div class="on-off" :class="[alignment, { 'is-enabled': modelValue, 'is-disabled': !modelValue }]">
        <div class="slider" @click="emits('update:modelValue', !modelValue)">
            <span>
                <IconCheck v-if="modelValue"/>
                <IconCross class="translate" v-else/>
            </span>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.on-off{
  display: inline-block;
  width: auto;
  height: 36px;
  line-height: 36px;
  text-align: center;

  &.is-enabled{
    .slider{
      background-color: $success;

      span{
        transform: translateX(26px);

        :deep(svg){
          fill: darken($success, 25%);
        }
      }
    }
  }

  &.is-disabled{
    .slider{
      background-color: darken($background, 1%);

      span{
        background-color: white;
        transform: translateX(0);

        :deep(svg){
          fill: darken($background, 20%);
        }
      }
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

  .slider{
    height: 24px;
    width: 50px;
    border-radius: 12px;
    margin: 6px 0;
    cursor: pointer;
    transition: background-color .2s linear;
    position: relative;

    span{
      display: flex;
      align-items: center;
      justify-content: center;
      width: 18px;
      height: 18px;
      border-radius: 100%;
      background-color: white;
      position: absolute;
      top: 3px;
      left: 3px;
      transition: transform .2s linear;

      :deep(svg){
        width: 14px;
        height: 14px;

        &.translate{
          transform: translateY(0);
        }
      }
    }
  }
}
</style>