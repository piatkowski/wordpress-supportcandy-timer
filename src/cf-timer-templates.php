<?php

final class WPSC_CF_Timer_Templates {

	public static function ticket_list( $cf, $ticket ) {

		if ( ! is_object( $ticket->{$cf->slug} ) ) {
			return '';
		}

		return self::render_timer( $cf, $ticket, 'list' );
	}

	public static function ticket_widget( $cf, $ticket ) {
		if ( ! is_object( $ticket->{$cf->slug} ) ) {
			return '';
		}

		return self::render_timer( $cf, $ticket, 'widget' );
	}

	private static function render_timer( $cf, $ticket, $view ) {

		$status_id   = (int) $ticket->status->id;
		$priority_id = (int) $ticket->priority->id;

		if ( isset( Timer_Support_Candy::DAYS[ 'status_' . $status_id ] ) ) {
			$config = Timer_Support_Candy::DAYS[ 'status_' . $status_id ];
		} elseif ( isset( Timer_Support_Candy::DAYS[ 'priority_' . $priority_id ] ) ) {
			$config = Timer_Support_Candy::DAYS[ 'priority_' . $priority_id ];
		} else {
			$config = Timer_Support_Candy::DAYS[0];
		}

		$start_date = $ticket->{$cf->slug};


		if ( $config['mode'] == 'DK' ) {
			$repair_date = Timer_Support_Candy_Helper::add_days( $start_date, $config['days'] );
		} else if ( $config['mode'] == 'DR' ) {
			$repair_date = Timer_Support_Candy_Helper::add_working_days( $start_date, $config['days'] );
		} else {
			if ( $view === 'widget' ) {
				return $start_date->format( 'd.m.Y' );
			}

			return '-';
		}

		$present_day = new DateTime();
		$present_day->setTime( 0, 0 );

		$days = Timer_Support_Candy_Helper::get_days( $present_day, $repair_date );

		if ( $days <= 0 ) {
			$css_class = 'wpsc_red';
		} elseif ( $days <= 7 ) {
			$css_class = 'wpsc_yellow';
		} else {
			$css_class = 'wpsc_green';
		}

		$progress = round( abs($days) / $config['days'] * 100 );
		if ( $progress < 0 ) {
			$progress = 0;
		} elseif ( $progress > 100 ) {
			$progress = 100;
		}


		$html = '<div class="wpsc_timer ' . esc_attr( $view ) . ' ' . $css_class . '">';
		$html .= '<span class="meter" style="width: ' . $progress . '%">';
		$html .= intval( $days ) . ' dni';
		$html .= '</span>';
		$html .= '</div>';
		$current_user = WPSC_Current_User::$current_user;
		if ( $view === 'widget' && $current_user->is_agent) {
			$html .= '<small>';
			$html .= $start_date->format( 'd.m.Y' ) . ' + ';
			$html .= $config['days'] . ' ' . ($config['mode'] !== 'off' ? $config['mode'] : '') . ' => ';
			$html .= $repair_date->format( 'd.m.Y' );
			$html .= '</small>';
		}

		return $html;
	}

}