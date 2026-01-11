( function () {
    'use strict';

    mw.agora.ve = {
        ui: {}
    };

    mw.agora.ve.Target = function AgoraVeTarget( config ) {
        config = config || {};

        mw.agora.ve.Target.super.call( this, ve.extendObject( {
            toolbarConfig: { $overlay: true, position: 'bottom' }
        }, config ) );

        this.id = config.id;
    };

    OO.inheritClass( mw.agora.ve.Target, ve.init.mw.Target );

    mw.agora.ve.Target.static.name = 'agora';

    mw.agora.ve.Target.static.modes = [ 'visual', 'source' ];

    mw.agora.ve.Target.static.toolbarGroups = [
        {
            name: 'style',
            title: OO.ui.deferMsg( 'visualeditor-toolbar-style-tooltip' ),
            include: [ 'bold', 'italic', 'moreTextStyle' ],
        },
        {
            name: 'link',
            include: [ 'link' ]
        }
    ];

    mw.agora.ve.Target.static.importRules = ve.copy( mw.agora.ve.Target.static.importRules );
    mw.agora.ve.Target.static.importRules.external.blacklist[ 'link/mwExternal' ] = false;

    mw.agora.ve.Target.static.allowTabFocusChange = true;

    mw.agora.ve.Target.prototype.addSurface = function ( doc, config ) {
        config = ve.extendObject( {
            $overlayContainer: $( '#content' ),
            placeholder: mw.message( 'agora-ve-placeholder', mw.config.get( 'wgPageName' ) ).text()
        }, config );

        return mw.agora.ve.Target.super.prototype.addSurface.call( this, doc, config );
    };

    mw.agora.ve.Target.prototype.loadContent = function ( content ) {
        let doc = this.constructor.static.parseDocument( content, this.getDefaultMode(), null );
        this.originalHtml = content;

        this.documentReady( doc );
    };

    mw.agora.ve.Target.prototype.attachToolbar = function () {
        this.$element.after( this.getToolbar().$element );
    };

    mw.agora.ve.Target.prototype.surfaceReady = function () {
        mw.agora.ve.Target.super.prototype.surfaceReady.apply( this, arguments );
    }

    ve.init.mw.targetFactory.register( mw.agora.ve.Target );

}() );