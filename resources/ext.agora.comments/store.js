const { defineStore } = require( 'pinia' );

const useCommentStore = defineStore( 'comments', {
    state: () => ( {
        pageId: null,
        totalComments: null,
        canPost: false,
        isModerator: false,
        sortMode: 'newest',
        showDeleted: false
    } ),

    actions: {
        initFromMW() {
            this.pageId = mw.config.get( 'wgArticleId' );
            this.totalComments = mw.config.get( 'wgAgora' ).commentCount;

            // @TODO: dynamically get these from stuff to be added to mw.config later
            this.canPost = false;
            this.isModerator = false;
            this.showDeleted = false
        },

        setSortMode( mode ) {
            this.sortMode = mode;
        },

        toggleShowDeleted() {
            this.showDeleted = !this.showDeleted;
        }
    }
} );

module.exports = {
    useCommentStore
}
