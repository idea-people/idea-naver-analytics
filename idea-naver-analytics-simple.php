<?php

/*
Plugin Name: idea-naver-analytics-simple
Plugin URI: http://www.ideapeople.co.kr
Description: 네이버 애널리틱스 플러그인입니다. 애널리틱스 키를 발급받으시고 키를 넣으시면 애널리틱스 코드가 워드프레스에 반영되어 등록됩니다.
Version: 1.0
Author: ideapeople
Author URI: http://www.ideapeople.co.kr
*/

namespace ideapeople\naver\analytics;

class IdeaNaverAnalytics {
	public $plugin_name = 'NaverAnalytics';
	public $slug = 'NaverAnalytics';
	public $option_group = 'NaverAnalyticsGroup';
	public $option_name = 'NaverAnalytics';
	public $options = array();

	public function run() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		if ( $this->get_option( 'analytics_key' ) ) {
			add_action( 'wp_footer', array( $this, 'render_analytics' ) );
		}
	}

	public function render_analytics() { ?>
		<script type="text/javascript" src="http://wcs.naver.net/wcslog.js"></script>
		<script type="text/javascript">
			if (!wcs_add) var wcs_add = {};
			wcs_add["wa"] = "<?php echo $this->get_option( 'analytics_key' );?>";
			wcs_do();
		</script>
		<?php
	}

	public function admin_init() {
		if ( ! empty ( $GLOBALS['pagenow'] ) && ( 'options-general.php' === $GLOBALS['pagenow'] || 'options.php' === $GLOBALS['pagenow'] ) ) {
			$this->register_settings();
		}
	}

	public function admin_menu() {
		$this->settings_init();
	}

	public function settings_init() {
		add_options_page(
			$this->plugin_name,
			$this->plugin_name,
			'manage_options',
			$this->slug,
			array( $this, 'settings_page' )
		);
	}

	public function settings_page() { ?>
		<div class="wrap">
			<h2>네이버 애널리틱스
				<div class="fb-like" data-href="https://www.facebook.com/ipeople2014/" data-layout="button"
				     data-action="like"
				     data-size="large" data-show-faces="true" data-share="true" style="margin-left: 50px;"></div>
				<div id="fb-root"></div>
				<script>(function (d, s, id) {
						var js, fjs = d.getElementsByTagName(s)[0];
						if (d.getElementById(id)) return;
						js = d.createElement(s);
						js.id = id;
						js.src = "//connect.facebook.net/ko_KR/sdk.js#xfbml=1&version=v2.7&appId=1778149215766306";
						fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));</script>
			</h2>

			<p class="notice" style="border:1px solid #dedede;padding:10px;">
				이 플러그인은 <em>아이디어피플</em> 에서 무료로 배포하는 플러그인 입니다. <br/>
				<strong>좋아요</strong>와 <strong>공유하기</strong>를 눌러주시면 개발사에 큰 힘이 됩니다. <br>
				문의 사항은 <a href="http://www.ideapeople.co.kr" target="_blank">http://www.ideapeople.co.kr</a>에 방문하셔서 문의해
				주세요.
			</p>
		</div>
		<form action="options.php" method="POST" id="naverSyncForm">
			<?php
			settings_fields( $this->option_group );

			do_settings_sections( $this->slug ); ?>

			<?php submit_button(); ?>
		</form>
		<?php
	}

	public function validate_option( $values ) {
		$out = array();

		foreach ( $this->options as $key => $value ) {
			if ( empty ( $values[ $key ] ) ) {
				$out[ $key ] = $value;
			} else {
				$out[ $key ] = $values[ $key ];
			}
		}

		return $out;
	}

	public function view_section_1() { ?>
		<p>애널리틱스 코드 발급은 <a href="http://analytics.naver.com/" target="_blank">http://analytics.naver.com/</a> 에서 발급받고
			이용해 주세요.</p>
		<?php
	}

	public function register_settings() {
		register_setting( $this->option_group, $this->option_name, array( $this, 'validate_option' ) );

		add_settings_section(
			'section_1',
			'플러그인 환경설정',
			array( $this, 'view_section_1' ),
			$this->slug
		);
		?>
		<?php
		$this->create_field( 'field1', '네이버 애널리틱스 KEY', 'analytics_key', '_create_text_field' );
	}

	public function create_field( $id, $label, $option_name, $func ) {
		$this->options[ $option_name ] = '';

		add_settings_field( $id, $label, array( $this, $func ), $this->slug, 'section_1',
			array(
				'label_for'   => $option_name,
				'name'        => $option_name,
				'value'       => $this->get_option( $option_name ),
				'option_name' => $this->option_name
			)
		);
	}

	public function _create_text_field( $args ) {
		printf(
			'<input type="text" name="%1$s[%2$s]" id="%3$s" value="%4$s" class="regular-text">',
			$args['option_name'],
			$args['name'],
			$args['label_for'],
			$args['value']
		);
	}

	public function get_options() {
		return get_option( $this->option_name );
	}

	public function get_option( $key ) {
		$v = $this->get_options();

		if ( isset( $v[ $key ] ) ) {
			if ( is_array( $v[ $key ] ) ) {
				foreach ( $v[ $key ] as &$value ) {
					$value = esc_attr( $value );
				}

				return $v[ $key ];
			} else {
				return esc_attr( $v[ $key ] );
			}
		}

		return false;
	}
}

$plugin = new IdeaNaverAnalytics();
add_action( 'plugins_loaded', array( $plugin, 'run' ) );