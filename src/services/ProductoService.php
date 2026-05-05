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

    public function obtenerPorId(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }

    /**
     * @param int[] $ids
     * @return Product[]
     */
    public function obtenerPorIds(array $ids): array
    {
        return $this->productRepository->findByIds($ids);
    }

    /**
     * @return Product[]
     */
    public function listarRecientes(int $limit = 4): array
    {
        return $this->productRepository->findRecent($limit);
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

        //usa el metodo paginator para paginar los productos en las categorias
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

        try {
            $imageName = $this->handleImageUpload($imageFile);
        } catch (\RuntimeException $e) {
            return $e->getMessage();
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

    public function editar(int $id, array $data, ?array $imageFile = null): bool|string
    {
        $product = $this->productRepository->findById($id);
        if (!$product) {
            return 'El producto no existe.';
        }

        $category = $this->categoryRepository->findById((int)$data['category_id']);
        if (!$category) {
            return 'La categoria seleccionada no existe.';
        }

        //se pasa en el segundo argumento el nombre de la imagen antigua, si se añade imagen se borra
        //si no se queda como estaba
        try {
            $imageName = $this->handleImageUpload($imageFile, $product->image);
        } catch (\RuntimeException $e) {
            return $e->getMessage();
        }

        $updated = new Product(
            name: $data['name'],
            category_id: (int)$data['category_id'],
            description: $data['description'],
            price: (float)$data['price'],
            stock: (int)$data['stock'],
            image: $imageName,
            id: $id
        );

        return $this->productRepository->save($updated);
    }

    public function eliminar(int $id): bool|string
    {   

        $product = $this->productRepository->findById($id);
        if (!$product) {
            return 'El producto no existe.';
        }
        //cuanta si ya se ha pedido
        if ($this->productRepository->countOrderItemsByProduct($id) > 0) {
            return 'No se puede eliminar el producto porque está asociado a uno o más pedidos.';
        }
        //busca si existe la ruta de la imagen y con el unlink la elimina
        if (!empty($product->image)) {
            $imagePath = __DIR__ . '/../../public/uploads/images/' . $product->image;
            if (file_exists($imagePath)) {
                @unlink($imagePath);
            }
        }

        return $this->productRepository->delete($id);
    }

    /**
     * Procesa la subida de una imagen. Si se sube una nueva imagen, elimina la anterior.
     * @throws \RuntimeException con el mensaje de error si falla.
     */
    private function handleImageUpload(?array $imageFile, string $oldImage = ''): string
    {   
        // si no ve ningun archivo o el nombre esta vacio devuelve la imagen antigua
        if (!is_array($imageFile) || ($imageFile['name'] ?? '') === '') {
            return $oldImage;
        }
        // si hay algun error lanza excepcion
        if (($imageFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('No se pudo procesar la imagen subida.');
        }

        //extrae la extension y comprueba si esta permitida
        $extension = strtolower(pathinfo((string)$imageFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($extension, $allowed, true)) {
            throw new \RuntimeException('El formato de imagen no esta permitido.');
        }
        //comprueba que existe la carpeta de destino y sino crea una 0775
        $uploadDir = __DIR__ . '/../../public/uploads/images';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new \RuntimeException('No se pudo crear la carpeta de imagenes.');
        }

        try {
            $newName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
        } catch (\Exception) {
            $newName = time() . '_' . uniqid('', true) . '.' . $extension;
        }

        if (!move_uploaded_file($imageFile['tmp_name'], $uploadDir . DIRECTORY_SEPARATOR . $newName)) {
            throw new \RuntimeException('No se pudo guardar la imagen en el servidor.');
        }


        //si habia imagen antigua la borra del servidor
        if ($oldImage !== '') {
            $oldPath = $uploadDir . DIRECTORY_SEPARATOR . $oldImage;
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        //devuelve el nombre del nuevo archivo que es lo que se guarda en la base de datos
        return $newName;
    }
}
