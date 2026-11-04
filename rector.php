<?php
/**
 * Rector.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

declare( strict_types = 1 );

use Rector\Config\RectorConfig;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php80\Rector\Catch_\RemoveUnusedVariableInCatchRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\Rector\Ternary\TernaryToNullsafeCoalesceRector;

return RectorConfig::configure()
	->withPaths(
		[
			__DIR__ . '/src',
		]
	)
	->withPhpSets()
	->withTypeCoverageLevel( 0 )
	->withDeadCodeLevel( 0 )
	->withCodeQualityLevel( 0 )
	->withSkip(
		[
			/**
			 * Disallowed by our coding standard (see `phpcs.xml.dist`).
			 */
			ClassPropertyAssignToConstructorPromotionRector::class,
			ClosureToArrowFunctionRector::class,
			RemoveUnusedVariableInCatchRector::class,
			TernaryToNullsafeCoalesceRector::class,
		]
	);
