<?php

namespace App\Core;

class Router
{
	private array $routes = [];

	public function get(string $path, array $action): void
	{
		$this->addRoute('GET', $path, $action);
	}

	public function post(string $path, array $action): void
	{
		$this->addRoute('POST', $path, $action);
	}

	private function addRoute(string $method, string $path, array $action): void
	{
		$this->routes[] = [
			'method' => $method,
			'path' => trim($path, '/'),
			'controller' => $action[0],
			'action' => $action[1],
		];
	}

	public function dispatch(): void
	{
		$url = trim($_GET['url'] ?? '', '/');
		$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

		foreach ($this->routes as $route) {
			$pattern = $this->convertToRegex($route['path']);

			if ($route['method'] === $method && preg_match($pattern, $url, $matches)) {
				array_shift($matches);
				$controller = new $route['controller']();
				call_user_func_array([$controller, $route['action']], $matches);
				return;
			}
		}

		if (defined('BASE_URL')) {
			header('Location: ' . BASE_URL . '404');
			exit();
		}

		header('HTTP/1.1 404 Not Found');
		echo '404 - Pagina no encontrada';
	}

	private function convertToRegex(string $path): string
	{
		$pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '([^/]+)', $path);
		return '#^' . $pattern . '$#';
	}
}
