<?php

namespace Wporg\TranslationEvents;

class Events_Text_Snippet {

	/**
	 * Generate links for text snippets.
	 *
	 * @param array $snippets The array of snippets.
	 * @return string The generated snippet links.
	 */
	public static function generate_snippet_links( $snippets ) : string {
		$links = '<ul class="text-snippets">';
		foreach ( $snippets as $snippet ) {
			$links .= sprintf( '<li><a href="#" class="text-snippet" data-snippet="%s">%s</a></li>', $snippet['snippet'], $snippet['title'] );
		}
		$links .= '</ul>';
		return $links;
	}

	/**
	 * Get the default snippets.
	 *
	 * @return array The default snippets.
	 */
	public static function get_default_snippets(): array {
		return array(
			array(
				'title'   => 'General Instructions',
				'snippet' => 'These are general instructions for anyone intending to contribute',
			),
			array(
				'title'   => 'New Contributor Guidance',
				'snippet' => 'Here is the snippet for new contributor guidance',
			),
		);
	}
}

