<?php

namespace Telepedia\Extensions\Agora\Hooks;

use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;

class SchemaHandler implements LoadExtensionSchemaUpdatesHook {

	/**
	 * @inheritDoc
	 */
	public function onLoadExtensionSchemaUpdates( $updater ): void {
		$baseDir = dirname( __DIR__, 2 );
		$dbType = $updater->getDB()->getType();

		$updater->addExtensionTable(
			'agora_comments',
			"$baseDir/schema/$dbType/tables-generated.sql",
		);
	}
}