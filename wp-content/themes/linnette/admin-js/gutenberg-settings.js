wp.domReady( function() {
    wp.blocks.unregisterBlockType( 'core/verse' );
    wp.blocks.unregisterBlockType( 'core/image' );
    wp.blocks.unregisterBlockType( 'core/cover' );
    wp.blocks.unregisterBlockType( 'core/gallery' );
    wp.blocks.unregisterBlockType( 'core/embed' );
    wp.blocks.unregisterBlockType( 'core/audio' );
    wp.blocks.unregisterBlockType( 'core/file' );
    wp.blocks.unregisterBlockType( 'core/video' );
    wp.blocks.unregisterBlockType( 'core/pullquote' );
    wp.blocks.unregisterBlockType( 'core/table' );
    wp.blocks.unregisterBlockType( 'core/column' );
    wp.blocks.unregisterBlockType( 'core/columns' );
    wp.blocks.unregisterBlockType( 'core/more' );
    wp.blocks.unregisterBlockType( 'core/separator' );
    wp.blocks.unregisterBlockType( 'core/button' );
    wp.blocks.unregisterBlockType( 'core/spacer' );
    wp.blocks.unregisterBlockType( 'core/media-text' );
    wp.blocks.unregisterBlockType( 'core/nextpage' );
    wp.blocks.unregisterBlockType( 'core/archives' );
    wp.blocks.unregisterBlockType( 'core/categories' );
    wp.blocks.unregisterBlockType( 'core/latest-comments' );
    wp.blocks.unregisterBlockType( 'core/latest-posts' );

    wp.blocks.getBlockTypes().forEach( function( block_setting ) {
        const prefix_to_remove = 'core-embed/';
        if(
            block_setting.name.substring( 0, prefix_to_remove.length ) === prefix_to_remove
            && block_setting.name !== 'core-embed/youtube'
        ) {
            wp.blocks.unregisterBlockType( block_setting.name );
        }
    } );

} );