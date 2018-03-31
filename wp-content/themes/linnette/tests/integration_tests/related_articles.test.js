import { Selector } from 'testcafe';

fixture( 'Related articles' )
    .page( 'https://linnette.test/wp-login.php' )
    .beforeEach( async t => {

        const login = Selector('#user_login');
        const pass = Selector('#user_pass');
        await t
            .hover(login) //Othwerise login JS focuses out right before text input
            .click(login)
            .typeText(login, 'admin')
            .typeText(pass, 'admin')
            .click(Selector('#wp-submit'));
    } );

test( 'Related articles are not shown for new pages', async t => {

    // Create simple page
    await t.navigateTo( 'https://linnette.test/wp-admin/post-new.php?post_type=page' )
        .typeText( new Selector('#title'), 'Testovací Stránka new' )
        .switchToIframe('#content_ifr')
        .click(Selector('#tinymce'))
        .pressKey( 't e s t t e x t' )
        .switchToMainWindow()
        .click( new Selector('#publish'))
        .click( Selector('a').withText( 'Zobrazit stránku' ) )
        .expect( Selector( 'body' ).innerText ).notContains( 'Mohlo by vás zajímat' )
        .expect( Selector( 'body' ).innerText ).contains( 'testtext' );

} );

test( 'Related pages selection', async t => {

    await t.navigateTo( 'https://linnette.test/relatedarticles-no-related/' )
        .expect( Selector('body').innerText ).contains( 'test text' )
        .expect( Selector('body').innerText ).notContains( 'Mohlo by vás zajímat' )
        .click( Selector('a').withText( 'Upravit stránku' ) )
        .click( Selector('span.acf-rel-item').withExactText( 'RelatedArticles – page 1' ) )
        .click( Selector('#publish') )
        .click( Selector('a').withText( 'Zobrazit stránku' ) )
        .expect( Selector('body').innerText ).contains('test text')
        .expect( Selector('body').innerText ).contains('RelatedArticles – page 1')
        .expect( Selector('body').innerText ).contains('Mohlo by vás zajímat');

    await t.click( Selector('a').withText( 'Upravit stránku' ) )
        .click( Selector('span.acf-rel-item').withExactText( 'RelatedArticles – page 2' ) )
        .click( Selector('span.acf-rel-item').withExactText( 'RelatedArticles – page 3' ) )
        .click( Selector('#publish') )
        .click( Selector('a').withText( 'Zobrazit stránku' ) )
        .expect( Selector('body').innerText ).contains('test text')
        .expect( Selector('body').innerText ).contains('RelatedArticles – page 1')
        .expect( Selector('body').innerText ).contains('RelatedArticles – page 2')
        .expect( Selector('body').innerText ).contains('RelatedArticles – page 3')
        .expect( Selector('body').innerText ).contains('Mohlo by vás zajímat');

    // Delete page from selection
    const page1_rel = Selector('.acf-bl.list.ui-sortable span.acf-rel-item').withText( 'RelatedArticles – page 1' );
    await t.click( Selector('a').withText( 'Upravit stránku' ) )
        .hover( page1_rel )
        .hover( page1_rel.find('a') )
        .click( page1_rel.find('a') )
        .click( Selector('#publish') )
        .click( Selector('a').withText( 'Zobrazit stránku' ) )
        .expect( Selector('body').innerText ).contains('test text')
        .expect( Selector('body').innerText ).notContains('RelatedArticles – page 1');

} );