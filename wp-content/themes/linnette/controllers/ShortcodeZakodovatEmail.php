<?php

namespace Linnette\Controllers;


class ShortcodeZakodovatEmail {

	public static function getInstance()
	{
		static $instance = null;
		if (null === $instance) {
			$instance = new static();
		}

		return $instance;
	}

	protected function __construct() {

		add_shortcode( 'zakodovat_email', array( $this, 'zakodovat_emailSc_cb' ) );

	}

	public function zakodovat_emailSc_cb( $atts, $content = null ) {

		$atts = shortcode_atts( array(
			'block' => false
		), $atts, 'zakodovat_email' );

		$email = is_email( $content ) ? $content : false;

		if( !$email ) return '';


		$encoded_mail = $this->hide_email( $email );

		if( $atts['block'] ) {
			return '<p>' . $encoded_mail . '</p>';
		}

		return $encoded_mail;

	}

	private function hide_email($email)

	{ $character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';

		$key = str_shuffle($character_set); $cipher_text = ''; $id = 'e'.rand(1,999999999);

		for ($i=0;$i<strlen($email);$i+=1) $cipher_text.= $key[strpos($character_set,$email[$i])];

		$script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipher_text.'";var d="";';

		$script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));';

		$script.= 'document.getElementById("'.$id.'").innerHTML="<a href=\\"mailto:"+d+"\\">"+d+"</a>"';

		$script = "eval(\"".str_replace(array("\\",'"'),array("\\\\",'\"'), $script)."\")";

		$script = '<script type="text/javascript">/*<![CDATA[*/'.$script.'/*]]>*/</script>';

		return '<span id="'.$id.'">[javascript protected email address]</span>'.$script;

	}

}