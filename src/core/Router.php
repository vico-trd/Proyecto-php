<?php

namespace App\Core;

class Router
{
	private array $routes = [];

	/**
	 * Registra una ruta GET.
	 *
	 * @param string $path    Patrón de ruta (ej: 'producto/{id}')
	 * @param array  $action  [NombreClaseController::class, 'nombreMétodo']
	 * @return void
	 */



	//ESTOS DOS METODOS SON ATAJOS QUE LLAMAN A ADDROUTE()
	//PASANDOLE EL METODO HTTP CORRESPONDIENTE
	// EL ACTION ES UN ARRAY CON LA CLASE Y EL NOMBRE DEL METODO A LLAMAR
	public function get(string $path, array $action): void
	{
		$this->addRoute('GET', $path, $action);
	}

	/**
	 * Registra una ruta POST.
	 *
	 * @param string $path    Patrón de ruta (ej: 'categorias/crear')
	 * @param array  $action  [NombreClaseController::class, 'nombreMétodo']
	 * @return void
	 */
	public function post(string $path, array $action): void
	{
		$this->addRoute('POST', $path, $action);
	}


	//AQUI SE ENTIENDO EL ACTION
	private function addRoute(string $method, string $path, array $action): void
	{
		$this->routes[] = [
			'method' => $method,
			'path' => trim($path, '/'), //QUITA LAS BARRAS DEL PRINCIPIO Y DEL FINAL CON TRIM
			'controller' => $action[0],
			'action' => $action[1],
		];
	}

	/**
	 * Resuelve la petición actual buscando la ruta que coincida con
	 * el método HTTP y la URL, e instancia el controlador correspondiente.
	 * Si no hay coincidencia redirige a la página 404.
	 *
	 * @return void
	 */


	//DISPATCH ES EL CORAZON DEL ROUTER
	public function dispatch(): void
	{	
		// coge la url de aqui
		$url = trim($_GET['url'] ?? '', '/');
		// y el metodo http
		$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

		foreach ($this->routes as $route) {
			//hace un foreach de las rutas de antes y va haciendo el regex de cada ruta
			$pattern = $this->convertToRegex($route['path']);

			//va comparando el metodo con el metodo de la url
			if ($route['method'] === $method && preg_match($pattern, $url, $matches)) {
				//elimina el primer elemento pq matches tiene la url completa y solo nos interesan los grupos capturados
				array_shift($matches);
				$controller = new $route['controller']();
				//llama al metodo 
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

	/**
	 * Convierte un patrón de ruta con placeholders a una expresión regular.
	 * Por ejemplo: 'producto/{id}' → '#^producto/([^/]+)$#'
	 *
	 * @param string $path  Patrón de ruta con placeholders entre llaves
	 * @return string       Expresión regular lista para usar con preg_match()
	 */
	private function convertToRegex(string $path): string
	{
		$pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '([^/]+)', $path);
		return '#^' . $pattern . '$#';
	}

	
}
