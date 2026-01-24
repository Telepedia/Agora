<template>
  <div class="agora-comment__popover">
    <div class="agora-comment__popover-arrow"></div>
    <slot></slot>
  </div>
</template>

<script>
/**
 * This is a makeshift popover created for the dropdown of the settings/actions for a comment. Since the MW version
 * we are using doesn't yet have CdxPopover, this will have to do. When CdxPopover becomes available, we will remove
 * this and revert to that
 */
const { defineComponent } = require( 'vue' );
module.exports = defineComponent( {
  name: "Popover"
} );
</script>

<style lang="less">
@import 'mediawiki.skin.variables.less';
@arrow-size: 16px;

.agora-comment__popover {
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translateX( -50% );
  margin-top: 6px;

  background: @background-color-base;
  border: 1px solid @border-color-base;
  border-radius: 3px;
  box-shadow: none;
  padding: 6px;
  z-index: 10;
  display: none;
}

.agora-comment__popover-arrow {
  position: absolute;
  top: -( @arrow-size / 2 );
  left: 50%;
  transform: translateX( -50% ) rotate( 45deg );

  width: @arrow-size;
  height: @arrow-size;

  background: @background-color-base;
  border: 1px solid @border-color-base;
  border-bottom: none;
  border-right: none;

  // Exact same masking approach Codex uses
  clip-path: polygon( 0 0, 100% 0, 0 100% );
}
</style>
