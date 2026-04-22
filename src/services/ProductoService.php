<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use JasonGrimes\Paginator;

class ProductoService
{
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
        $this->categoryRepository = new CategoryRepository();
    }

    public function listar(): array
    {
        return $this->productRepository->findAll();
    }

    public function listarPorCategoriaPaginado(int $categoryId, int $currentPage, int $itemsPerPage = 6): array
    {
        $totalItems = $this->productRepository->countByCategory($categoryId);
        $currentPage = max(1, $currentPage);

        $totalPages = max(1, (int)ceil($totalItems / $itemsPerPage));
        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $offset = ($currentPage - 1) * $itemsPerPage;
        $products = $this->productRepository->findByCategoryPaginated($categoryId, $itemsPerPage, $offset);

        $urlPattern = BASE_URL . 'categoria/' . $categoryId . '/productos&page=(:num)';
        $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);

        return [
            'products' => $products,
            'paginator' => $paginator,
            'currentPage' => $currentPage,
            'totalItems' => $totalItems,
        ];
    }

    public function crear(array $data, ?array $imageFile = null): bool|string
    {
        $category = $this->categoryRepository->findById((int)$data['category_id']);
        if (!$category) {
            return 'La categoria seleccionada no existe.';
        }

        $imageName = '';

        if (is_array($imageFile) && ($imageFile['name'] ?? '') !== '') {
            if (($imageFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                return 'No se pudo procesar la imagen subida.';
            }

            $extension = strtolower(pathinfo((string)$imageFile['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (!in_array($extension, $allowed, true)) {
                return 'El formato de imagen no esta permitido.';
            }

            $uploadDir = __DIR__ . '/../../public/uploads/images';
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
                return 'No se pudo crear la carpeta de imagenes.';
            }

            try {
                $imageName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
            } catch (\Exception $e) {
                $imageName = time() . '_' . uniqid('', true) . '.' . $extension;
            }

            $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $imageName;
            if (!move_uploaded_file($imageFile['tmp_name'], $targetPath)) {
                return 'No se pudo guardar la imagen en el servidor.';
            }
        }

        $product = new Product(
            name: $data['name'],
            category_id: (int)$data['category_id'],
            description: $data['description'],
            price: (float)$data['price'],
            stock: (int)$data['stock'],
            image: $imageName
        );

        return $this->productRepository->save($product);
    }
}
