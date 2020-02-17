<?php

/**
 * Total donors endpoint
 *
 * @package Give
 */

namespace Give\API\Endpoints\Reports;

class TotalDonors extends Endpoint {

	protected $payments;

	public function __construct() {
		$this->endpoint = 'total-donors';
	}

	public function get_report( $request ) {

		// Check if a cached version exists
		$cached_report = $this->get_cached_report( $request );
		if ( $cached_report !== null ) {
			// Bail and return the cached version
			return new \WP_REST_Response(
				[
					'data' => $cached_report,
				]
			);
		}

		$start = date_create( $request['start'] );
		$end   = date_create( $request['end'] );
		$diff  = date_diff( $start, $end );

		$dataset = [];

		switch ( true ) {
			case ( $diff->days > 12 ):
				$interval = round( $diff->days / 12 );
				$data     = $this->get_data( $start, $end, 'P' . $interval . 'D' );
				break;
			case ( $diff->days > 7 ):
				$data = $this->get_data( $start, $end, 'PT12H' );
				break;
			case ( $diff->days > 2 ):
				$data = $this->get_data( $start, $end, 'PT3H' );
				break;
			case ( $diff->days >= 0 ):
				$data = $this->get_data( $start, $end, 'PT1H' );
				break;
		}

		// Cache the report data
		$result = $this->cache_report( $request, $data );

		return new \WP_REST_Response(
			[
				'data' => $data,
			]
		);
	}

	public function get_data( $start, $end, $intervalStr ) {

		$this->payments = $this->get_payments( $start->format( 'Y-m-d H:i:s' ), $end->format( 'Y-m-d H:i:s' ) );

		$tooltips = [];
		$donors   = [];

		$interval = new \DateInterval( $intervalStr );

		$periodStart = clone $start;
		$periodEnd   = clone $start;

		// Subtract interval to set up period start
		date_sub( $periodStart, $interval );

		while ( $periodStart < $end ) {

			$donorsForPeriod = $this->get_donors( $periodStart->format( 'Y-m-d H:i:s' ), $periodEnd->format( 'Y-m-d H:i:s' ) );

			switch ( $intervalStr ) {
				case 'PT12H':
					$periodLabel = $periodStart->format( 'D ga' ) . ' - ' . $periodEnd->format( 'D ga' );
					break;
				case 'PT3H':
					$periodLabel = $periodStart->format( 'D ga' ) . ' - ' . $periodEnd->format( 'D ga' );
					break;
				case 'PT1H':
					$periodLabel = $periodStart->format( 'D ga' ) . ' - ' . $periodEnd->format( 'D ga' );
					break;
				default:
					$periodLabel = $periodStart->format( 'M j, Y' ) . ' - ' . $periodEnd->format( 'M j, Y' );
			}

			$donors[] = [
				'x' => $periodEnd->format( 'Y-m-d H:i:s' ),
				'y' => $donorsForPeriod,
			];

			$tooltips[] = [
				'title'  => $donorsForPeriod . ' ' . __( 'Donors', 'give' ),
				'body'   => __( 'Total Donors', 'give' ),
				'footer' => $periodLabel,
			];

			// Add interval to set up next period
			date_add( $periodStart, $interval );
			date_add( $periodEnd, $interval );
		}

		$totalDonorsForPeriod = $this->get_donors( $start->format( 'Y-m-d H:i:s' ), $end->format( 'Y-m-d H:i:s' ) );
		$trend                = $this->get_trend( $start, $end, $donors );

		// Create data objec to be returned, with 'highlights' object containing total and average figures to display
		$data = [
			'datasets' => [
				[
					'data'      => $donors,
					'tooltips'  => $tooltips,
					'trend'     => $trend,
					'highlight' => $totalDonorsForPeriod,
				],
			],
		];

		return $data;

	}

	public function get_trend( $start, $end, $donors ) {

		$interval = ( $end->getTimestamp() - $start->getTimestamp() ) / 3600;
		$slopes   = [];

		foreach ( $donors as $key => $value ) {
			if ( $key > 1 ) {
				$currentY = $income[ $key ]['y'];
				$prevY    = $income[ $key - 1 ]['y'];

				$diff  = $prevY - $currentY;
				$slope = $diff / $interval;

				$slopes[] += $slope;
			}
		}

		$sum   = round( array_sum( $slopes ), 2 );
		$count = count( $slopes );

		$trend = round( ( $sum / $count ) * 100, 1 );

		return $trend;
	}

	public function get_donors( $startStr, $endStr ) {

		$donors = [];
		foreach ( $this->payments as $payment ) {
			if ( $payment->date > $startStr && $payment->date < $endStr ) {
				$donors[] = $payment->donor_id;
			}
		}

		$unique     = array_unique( $donors );
		$donorCount = count( $unique );

		return $donorCount;
	}
}
