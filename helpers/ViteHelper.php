<?php

namespace Grocy\Helpers;

class ViteHelper
{
	private static $manifest = null;
	private static $devServerChecked = false;
	private static $devServerRunning = false;

	/**
	 * Check if Vite dev server is running
	 */
	public static function IsDevServerRunning(): bool
	{
		if (self::$devServerChecked)
		{
			return self::$devServerRunning;
		}

		self::$devServerChecked = true;

		// In production mode, dev server is never running
		if (defined('GROCY_MODE') && GROCY_MODE === 'production')
		{
			self::$devServerRunning = false;
			return false;
		}

		// Check if dev server is accessible
		$devServerUrl = 'http://localhost:5173';
		$context = stream_context_create([
			'http' => [
				'timeout' => 0.5,
				'ignore_errors' => true
			]
		]);

		$response = @file_get_contents($devServerUrl . '/@vite/client', false, $context);
		self::$devServerRunning = $response !== false;

		return self::$devServerRunning;
	}

	/**
	 * Get the manifest file contents
	 */
	private static function GetManifest(): ?array
	{
		if (self::$manifest !== null)
		{
			return self::$manifest;
		}

		$manifestPath = __DIR__ . '/../public/build/.vite/manifest.json';
		if (!file_exists($manifestPath))
		{
			return null;
		}

		self::$manifest = json_decode(file_get_contents($manifestPath), true);
		return self::$manifest;
	}

	/**
	 * Generate HTML tags for Vite assets
	 *
	 * @param string $entry The entry point (e.g., 'resources/js/app.js')
	 * @param string $baseUrl The base URL for assets
	 * @return string HTML tags for the assets
	 */
	public static function Assets(string $entry, string $baseUrl = '/'): string
	{
		$baseUrl = rtrim($baseUrl, '/');

		if (self::IsDevServerRunning())
		{
			return self::DevAssets($entry);
		}

		return self::ProductionAssets($entry, $baseUrl);
	}

	/**
	 * Generate dev server script tags
	 */
	private static function DevAssets(string $entry): string
	{
		$devServerUrl = 'http://localhost:5173';

		return <<<HTML
<script type="module" src="{$devServerUrl}/@vite/client"></script>
<script type="module" src="{$devServerUrl}/{$entry}"></script>
HTML;
	}

	/**
	 * Generate production asset tags from manifest
	 */
	private static function ProductionAssets(string $entry, string $baseUrl): string
	{
		$manifest = self::GetManifest();

		if ($manifest === null || !isset($manifest[$entry]))
		{
			return "<!-- Vite manifest not found or entry '{$entry}' missing -->";
		}

		$entryData = $manifest[$entry];
		$html = '';

		// Add CSS files
		if (isset($entryData['css']))
		{
			foreach ($entryData['css'] as $cssFile)
			{
				$html .= '<link rel="stylesheet" href="' . $baseUrl . '/build/' . $cssFile . '">' . "\n";
			}
		}

		// Add the main JS file
		if (isset($entryData['file']))
		{
			$html .= '<script type="module" src="' . $baseUrl . '/build/' . $entryData['file'] . '"></script>' . "\n";
		}

		return $html;
	}

	/**
	 * Generate preload tags for imported modules (performance optimization)
	 */
	public static function Preload(string $entry, string $baseUrl = '/'): string
	{
		if (self::IsDevServerRunning())
		{
			return '';
		}

		$manifest = self::GetManifest();
		if ($manifest === null || !isset($manifest[$entry]))
		{
			return '';
		}

		$baseUrl = rtrim($baseUrl, '/');
		$entryData = $manifest[$entry];
		$html = '';

		// Preload imported chunks
		if (isset($entryData['imports']))
		{
			foreach ($entryData['imports'] as $import)
			{
				if (isset($manifest[$import]['file']))
				{
					$html .= '<link rel="modulepreload" href="' . $baseUrl . '/build/' . $manifest[$import]['file'] . '">' . "\n";
				}
			}
		}

		return $html;
	}
}
