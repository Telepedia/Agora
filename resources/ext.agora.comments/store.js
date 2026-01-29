const { defineStore } = require( 'pinia' );
const restClient = require( 'telepedia.fetch' );
const Comment = require( './comment.js' );

const useCommentStore = defineStore( 'comments', {
    state: () => ( {
        pageId: null,
        pageTitle: null,
        totalComments: null,
        canPost: false,
        isModerator: false,
        sortMode: 'newest',
        hideDeleted: false,
        comments: null,
        isEditorOpen: false,
        currentUserAvatar: null
    } ),

    actions: {
        initFromMW() {
            this.pageId = mw.config.get( 'wgArticleId' );
            this.totalComments = mw.config.get( 'wgAgora' ).commentCount;
            this.isModerator = mw.config.get('wgAgora').isMod;
            this.currentUserAvatar = mw.config.get( 'wgAgora' ).userAvatar;
            this.pageTitle = mw.config.get( 'wgPageName' );

            // @TODO: dynamically get these from stuff to be added to mw.config later
            this.canPost = false;
            this.hideDeleted = false;
        },

        setSortMode( mode ) {
            this.sortMode = mode;
        },

        toggleShowDeleted() {
            this.showDeleted = !this.showDeleted;
        },

        toggleEditorOpen() {
            this.isEditorOpen = !this.isEditorOpen;
        },

        /**
         * Fetch the comments from the API
         * @returns {Promise<void>}
         */
        async fetchComments() {
            let url = `/comments/v0/comments/${this.pageId}`;
            if ( this.isModerator ) {
                url += `?hideDeleted=${this.hideDeleted}`;
            }

            const response = await restClient.get( url );

            this.comments = response.comments.map(
                data => new Comment( data )
            );

            this.commentCount = response.comments.length;
        },

        /**
         * Delete a comment from the stack - note this doesn't do much except mark the comment as deleted, and then
         * hide it in the UI until the next refresh, at which point it will either be returned from the API or not
         * depending on the users preferences to show or hide deleted comments
         * @param id
         * @returns {Promise<void>}
         */
        async deleteComment( id ) {
            try {
                await restClient.delete( `/comments/v0/comments/delete`, {
                    commentId: id,
                    token: mw.user.tokens.get( 'csrfToken' )
                } );

                const comment = this.comments.find( c => c.id === id );
                if ( comment ) {
                    comment.isDeleted = true;
                }

                new Toast('Comment has been successfully deleted', {
                    type: 'success',
                    position: 'bottom-left'
                } ).show();

            } catch ( e ) {
                new Toast(e.message || "An error occurred whilst deleting the comment", {
                    type: 'error',
                    position: 'bottom-left'
                } ).show();
            }
        },

        /**
         * Restore a comment back to the stack
         * @param id the id of the comment which we want to restore
         * @returns {Promise<void>}
         */
        async restoreComment( id ) {
            try {
                await restClient.patch( `/comments/v0/comments/restore`, {
                    commentId: id,
                    token: mw.user.tokens.get( 'csrfToken' )
                } );

                const comment = this.comments.find( c => c.id === id );
                if ( comment ) {
                    comment.isDeleted = false;
                }

                new Toast('Comment has been successfully restored', {
                    type: 'success',
                    position: 'bottom-left'
                } ).show();

            } catch ( e ) {
                new Toast( e.message || "An error occurred whilst restoring the comment", {
                    type: 'error',
                    position: 'bottom-left'
                } ).show();
            }
        }
    }
} );

module.exports = {
    useCommentStore
}
