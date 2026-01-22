<template>
  <div class="agora-comments-list">
    <div class="comment-wrapper" v-for="comment in comments" :key="comment.id">
      <div class="comment">
        <div class="comment__body">
          <div class="comment__body-header">
            <div class="comment__body-details">
              <div v-html="comment.getAvatar()"></div>
              <div v-html="comment.getLinkToUserPage()"></div>
              <div class="time" v-html="comment.getFormattedTime()"></div>
            </div>
            <div>
              <cdx-icon
                  class="comment__body-actions"
                  :icon="cdxIconEllipsis"
                  size="small"
              ></cdx-icon>
            </div>
          </div>
          <div v-html="comment.html"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
const { useCommentStore } = require("./../store.js");
const { defineComponent, computed, onMounted } = require( 'vue' );
const { cdxIconEllipsis } = require( '../../icons.json' );
const { CdxIcon } = require( '../../codex.js' );
module.exports = defineComponent( {
  name: "CommentList",
  components: {
    CdxIcon
  },
  setup() {
    const store = useCommentStore();

    const comments = computed( () => store.comments );

    onMounted(async () => {
      try {
        await store.fetchComments();
      } catch ( e ) {
        console.error( e );
      }
    } );

    return {
      comments,
      cdxIconEllipsis
    }
  }
} );
</script>

<style lang="less">
@import 'mediawiki.skin.variables.less';
.comment-wrapper {
  margin-bottom: 18px;

  p {
    margin: 0;
  }
}

.comment__body {
  padding: 18px;
  background: @background-color-neutral;
  width: 100%;
}

.comment {
  display: flex;
  flex-direction: row;
}

.comment__body-header {
  display: flex;
  flex-direction: row;
  align-items: center;
  margin-bottom: 14px;
}

.comment__body-details {
  display: flex;
  flex-direction: row;
  align-items: center;
  flex-grow: 1;
}

.comment__body-actions:hover {
  cursor: pointer;
}

.comment__body-details .time::before {
  content: "•";
  margin: 0 8px;
  font-size: 1rem;
  line-height: 1;
}
</style>