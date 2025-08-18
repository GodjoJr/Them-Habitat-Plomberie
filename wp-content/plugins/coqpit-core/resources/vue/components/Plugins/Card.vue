<script setup>
import IconTuning from '../Icons/IconTuning.vue'
import IconTrash from '../Icons/IconTrash.vue'
import IconDownload from '../Icons/IconDownload.vue'
import Button from '../Elements/Button.vue'
import OnOff from '../Elements/OnOff.vue'

import { ref } from 'vue'

const activated = ref(true)
</script>

<template>
    <div class="plugin-card">
        <div class="plugin-picture">
            <div class="picture">
                <!--<span class="installed">Extension inactive</span>-->
                <!--<span class="activated">Extension activée</span>-->
                <!--<span class="missing">Extension non installée</span>-->
                <img src="https://plugins.coqpit.fr/V6QapaZrZm5VZa/coqpit-social-wall/banner-772x250.jpg" alt="Social Wall">
            </div>
        </div>
        <div class="plugin-data">
            <p class="plugin-name">Social Wall <span>1.0.0</span></p>
            <p class="plugin-description">Importer les données des réseaux sociaux dans WordPress via un compte Juicer.io dans le but d'afficher les publications sur le thème WordPress.</p>
        </div>
        <div class="actions">
            <Button v-if="activated">
                <template v-slot:icon><IconTuning/></template>
                <template v-slot:default>Configurer</template>
            </Button>
            <Button :icon="true" :danger="true" v-if="!activated">
                <template v-slot:icon><IconTrash/></template>
            </Button>
            <OnOff v-model="activated" align="right"></OnOff>
            <!--
            <Button :icon="true" align="right">
                <template v-slot:icon><IconDownload/></template>
            </Button>
            -->
        </div>
    </div>
</template>

<style lang="scss" scoped>
.plugin-card{
  width: 100%;
  border: 1px solid darken(white, 10%);
  margin: 0;
  border-radius: 10px;
  position: relative;
  box-sizing: border-box;
  overflow: hidden;
  box-shadow: 0 1px 2px rgba($black, .05);

  .plugin-picture{
    width: 100%;

    .picture{
      width: 100%;
      height: 120px;
      background-color: darken(white, 4%);
      box-sizing: border-box;
      position: relative;

      span{
        display: inline-block;
        font-size: 11px;
        height: 22px;
        padding: 0 7px;
        line-height: 24px;
        border-radius: 3px;
        vertical-align: middle;
        background-color: rgba($black, 0.8);
        color: white;
        font-weight: 400;
        transform: translateY(-2px);
        position: absolute;
        bottom: 10px;
        right: 10px;

        &.activated{
          color: white;
          background-color: $success;
        }

        &.installed{
          color: white;
          background-color: $danger;
        }
      }

      img{
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
    }
  }

  .plugin-data{
    padding: 20px;

    p{
      line-height: 20px;
      margin: 0;

      &.plugin-name{
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 5px;

        span{
          display: inline-block;
          font-size: 11px;
          height: 17px;
          padding: 0 4px 0 4px;
          line-height: 19px;
          border-radius: 3px;
          vertical-align: middle;
          background-color: $black;
          color: white;
          font-weight: 400;
          transform: translateY(-2px);
          margin-left: 5px;
        }
      }

      &.plugin-description{
        opacity: .7;
        font-size: 13px;
      }
    }
  }

  .actions{
    width: 100%;
    border-top: 1px solid darken(white, 10%);
    padding: 20px;
    box-sizing: border-box;
  }
}
</style>