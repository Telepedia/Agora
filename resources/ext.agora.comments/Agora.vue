<template>
  <h3>{{ $i18n( 'agora-comments-header', totalCount ).text() }}</h3>
</template>

<script>
const { defineComponent } = require( 'vue' );
const { useCommentStore } = require( './store.js' );
module.exports = defineComponent( {
  name: 'Agora',
  setup() {
    console.log("Agora initialised....");

    // setup the store and initialise its data
    const store = useCommentStore();
    store.initFromMW();

    return {
      totalCount: store.totalComments
    }
  }
} );
</script>

<style lang="less">

</style>