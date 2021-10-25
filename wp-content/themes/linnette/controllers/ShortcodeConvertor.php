<?php

namespace Linnette\Controllers;

use Linnette\Traits\SingletonTrait;

class ShortcodeConvertor {
	use SingletonTrait;

	public function __construct() {

		\Routes::map( 'wp-admin/convert-shortcodes/:post_id', [ $this, 'maybeConvertShortcodes' ] );

		add_action( 'add_meta_boxes', [ $this, 'addConvertorMetabox'] );

	}

	public function maybeConvertShortcodes( $params ) {

		if( current_user_can( 'edit_posts' ) ) {

			$output = $this->convertShortcodesInPost( $params['post_id'] );
			wp_die( $output );

		} else {
			wp_die( 'Nepovolený přístup' );
		}

	}

	public function addConvertorMetabox() {

		add_meta_box(
			'linnette_shortcode_convertor',
			'Opravit tagy',
			function() {
				echo '<p><a href="' . trailingslashit(get_admin_url()) . 'convert-shortcodes/' . get_the_ID() . '">Opravit tagy v příspěvku</a></p>
						<small>(nejdřív si všechno ulož...)</small>';
			},
			[ 'blog', 'page' ],
			'side'
		);

	}

	private function convertShortcodesInPost( $post_id ) {

		$output = '';

		$posts = new \WP_Query( [
			'post__in' => [ $post_id ],
			'post_type' => [ 'blog', 'page' ],
			'nopaging' => true
		] );

		/** @var \WP_Post $post */
		foreach( $posts->posts as $post ) {

			$output .= 'Zpracovávám příspěvěk: ' . $post->post_title . '</br>';

			$new_post_content = $post->post_content;

			preg_match_all( '/\<\!\-\-\swp\:shortcode\s\-\-\>[\s\S]*?\[(.*?)(\s.*?)?\/?\](?:(.*?)(?:\[\/))?[\s\S]*?\<\!\-\-\s\/wp\:shortcode\s\-\-\>/m', $post->post_content, $matches );

			$shortcodes = array_map( null, $matches[0], $matches[1], $matches[2], $matches[3] );

			foreach( $shortcodes as $shortcode ) {

				$output .= 'Zpracovávám shortcode typu ' . $shortcode[1] . '</br>';

				preg_match_all( '/\s(.*?)\=\"(.*?)\"/', $shortcode[2], $attributes_matches );
				$attributes_array = array_map( null, $attributes_matches[1], $attributes_matches[2] );

				$attributes = [];
				foreach( $attributes_array as $attribute ) {
					$attributes[ $attribute[0] ] = $attribute[1];
				}

				if( ! method_exists( $this, 'migrate_' . $shortcode[1] ) ) {
					$output .= '<strong>Nevím, co mám dělat se shortcodem: ' . $shortcode[1] . '</strong></br>';
					continue;
				}

				$converted = $this->{'migrate_' . $shortcode[1]}( $attributes, $shortcode[3] );

				$new_post_content = str_replace( $shortcode[0], $converted, $new_post_content );

			}

			$output .= 'Ukládám změny do příspěvku s ID: ' . $post_id . '</br>';

			wp_update_post( [
				'ID' => $post->ID,
				'post_content' => $new_post_content
			] );

		}

		$output .= 'Zpracováno, <a href="' . get_edit_post_link( $post->ID ) . '">vrať se na příspěvěk a radši ho znovu ulož...</a>';

		return $output;

	}

	public function migrate_gallery( $attributes, $content = null ): string {

		$image_ids_array = explode( ',', $attributes['ids'] );
		$image_ids_string = '"' . implode( '","', $image_ids_array ) . '"';

		$columns = $attributes['columns'] ?? '1';

		$output = sprintf( '<!-- wp:acf/linn-gallery {
    "id": "block_5c8cb9e4aa387",
    "data": {
        "images": [
            %s
        ],
        "_images": "field_5c25f301966e4",
        "column_count": "%s",
        "_column_count": "field_5c25f336966e5"
    },
    "name": "acf\/linn-gallery",
    "align": "",
    "mode": "preview"
} /-->',
			$image_ids_string,
			$columns
		);

		return $output;

	}

	public function migrate_tupliky( $attributes = null, $content = null ) {

		return '<!-- wp:acf/linn-tupliky {
    "id": "block_5c8e7edce64fe",
    "data": [],
    "name": "acf\/linn-tupliky",
    "align": "",
    "mode": "preview"
} /-->';

	}

	public function migrate_photo_with_description( $attributes = null, $content = null ) {


//		$text = json_encode( $content );
		$text = $content;
		$signature = json_encode( $attributes['signature'] ?? '' );
		$type = ( $attributes['type'] ?? '' === 'reverse' ) ? 'reverse' : 'normal';
		$is_review = ( $attributes['is_review'] ?? '' === 'true' ) ? '1' : '0';

//		wp_die( $content );
//		wp_die( $text );

		$data = [
			'id'    => 'block_5c8e7ee7e64ff',
			'data'  => [
		    	"attachment" => $attributes['attachment'],
		        "_attachment" => "field_5c56caf490100",
		        "text" => $text,
		        "_text" => "field_5c56cc39d0c0e",
		        "signature" => $signature,
		        "_signature" => "field_5c56cb3b90101",
		        "type" => $type,
		        "_type" => "field_5c56cb5990102",
		        "is_review" => $is_review,
		        "_is_review" => "field_5c56cb8e90103"
		    ],
			"name"  => "acf\/linn-photo-with-description",
			"align" => "",
			"mode"  => "preview"
		];

		//After convert, data object is empty
		$output = '<!-- wp:acf/linn-photo-with-description ' . json_encode( $data ) . ' /-->';
		return $output;

		$output = sprintf( '<!-- wp:acf/linn-photo-with-description {
    "id": "block_5c8e7ee7e64ff",
    "data": {
        "attachment": %s,
        "_attachment": "field_5c56caf490100",
        "text": %s,
        "_text": "field_5c56cc39d0c0e",
        "signature": %s,
        "_signature": "field_5c56cb3b90101",
        "type": "%s",
        "_type": "field_5c56cb5990102",
        "is_review": "%s",
        "_is_review": "field_5c56cb8e90103"
    },
    "name": "acf\/linn-photo-with-description",
    "align": "",
    "mode": "preview"
} /-->', $attributes['attachment'], $text, $signature, $type, $is_review );

		//TODO: json encoding of entitites doesnt work very well

//		wp_die($output);


		return $output;

		/*
		 * <!-- wp:acf/linn-photo-with-description {
    "id": "block_5c8e7ee7e64ff",
    "data": {
        "attachment": 10965,
        "_attachment": "field_5c56caf490100",
        "text": "Spolupr\u00e1ce s Nikol pro m\u011b byla velmi p\u0159\u00edjemn\u00fdm a p\u0159\u00ednosn\u00fdm z\u00e1\u017eitkem. Nikol se na focen\u00ed precizn\u011b p\u0159ipravila, z celkov\u00e9ho p\u0159\u00edstupu \u010di\u0161\u00ed profesionalita a zku\u0161enost. Z\u00e1rove\u0148 je ale osobn\u011b tak mil\u00e1, \u017ee mi ned\u011blalo pot\u00ed\u017ee se uvolnit. V\u00fdsledn\u00e9 fotografie jsou n\u00e1dhern\u00e9, v\u00fdsti\u017en\u00e9, citliv\u00e9. A\u010dkoli s t\u00edm m\u00e1m jinak probl\u00e9m, na jej\u00edch fotografi\u00edch si p\u0159ipad\u00e1m kr\u00e1sn\u00e1. M\u016f\u017eu d\u00e1t jeden\u00e1ct hv\u011bzdi\u010dek z deseti?",
        "_text": "field_5c56cc39d0c0e",
        "signature": "Kate\u0159ina Loukotov\u00e1, www.dandylion.cz",
        "_signature": "field_5c56cb3b90101",
        "type": "normal",
        "_type": "field_5c56cb5990102",
        "is_review": "1",
        "_is_review": "field_5c56cb8e90103"
    },
    "name": "acf\/linn-photo-with-description",
    "align": "",
    "mode": "preview"
} /-->

<!-- wp:acf/linn-photo-with-description {
    "id": "block_5c8e7f37e6500",
    "data": {
        "attachment": 10965,
        "_attachment": "field_5c56caf490100",
        "text": "Spolupr\u00e1ce s Nikol pro m\u011b byla velmi p\u0159\u00edjemn\u00fdm a p\u0159\u00ednosn\u00fdm z\u00e1\u017eitkem. Nikol se na focen\u00ed precizn\u011b p\u0159ipravila, z celkov\u00e9ho p\u0159\u00edstupu \u010di\u0161\u00ed profesionalita a zku\u0161enost. Z\u00e1rove\u0148 je ale osobn\u011b tak mil\u00e1, \u017ee mi ned\u011blalo pot\u00ed\u017ee se uvolnit. V\u00fdsledn\u00e9 fotografie jsou n\u00e1dhern\u00e9, v\u00fdsti\u017en\u00e9, citliv\u00e9. A\u010dkoli s t\u00edm m\u00e1m jinak probl\u00e9m, na jej\u00edch fotografi\u00edch si p\u0159ipad\u00e1m kr\u00e1sn\u00e1. M\u016f\u017eu d\u00e1t jeden\u00e1ct hv\u011bzdi\u010dek z deseti?",
        "_text": "field_5c56cc39d0c0e",
        "signature": "Kate\u0159ina Loukotov\u00e1, www.dandylion.cz",
        "_signature": "field_5c56cb3b90101",
        "type": "reverse",
        "_type": "field_5c56cb5990102",
        "is_review": "0",
        "_is_review": "field_5c56cb8e90103"
    },
    "name": "acf\/linn-photo-with-description",
    "align": "",
    "mode": "preview"
} /-->

<!-- wp:shortcode -->
[photo_with_description attachment="10957" signature="Kateřina Loukotová, www.dandylion.cz"]Spolupráce s Nikol pro mě byla velmi příjemným a přínosným zážitkem. Nikol se na focení precizně připravila, z celkového přístupu čiší profesionalita a zkušenost. Zároveň je ale osobně tak milá, že mi nedělalo potíže se uvolnit. Výsledné fotografie jsou nádherné, výstižné, citlivé. Ačkoli s tím mám jinak problém, na jejích fotografiích si připadám krásná. Můžu dát jedenáct hvězdiček z deseti?[/photo_with_description]
<!-- /wp:shortcode -->
		 */

	}


}