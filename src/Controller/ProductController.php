<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use App\Service\ApiTokenService;

class ProductController extends AbstractController
{
    private ApiTokenService $apiTokenService;

    public function __construct(ApiTokenService $apiTokenService)
    {
        $this->apiTokenService = $apiTokenService;
    }

    #[Route('/api/products', name: 'list_products', methods: ['GET'])]
    public function listProducts(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->isTokenValid($request)) {
            return $this->json(['error' => 'Invalid or missing token'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $products = $entityManager->getRepository(Product::class)->findAll();

        $productArray  = [];

        foreach ($products as $product) {
            $productArray[] = [
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'product_name' => $product->getProductName(),
                'description' => $product->getDescription(),
                'created_at' => $product->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $product->getUpdatedAt() ? $product->getUpdatedAt()->format('Y-m-d H:i:s') : null,
            ];
        }

        return $this->json($productArray);
    }

    #[Route('/api/products', name: 'add_products', methods: ['POST'])]
    public function addProducts(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->isTokenValid($request)) {
            return $this->json(['error' => 'Invalid or missing token'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $content = json_decode($request->getContent(), true);

        if (is_null($content)) {
            return $this->json(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }

        foreach ($content as $productData) {
            $product = new Product();
            $product->setSku($productData['sku']);
            $product->setProductName($productData['product_name'] ?? null);
            $product->setDescription($productData['description'] ?? null);

            $entityManager->persist($product);
        }

        $entityManager->flush();

        return $this->json(['success' => 'Products added successfully']);
    }

    #[Route('/api/products', name: 'update_products', methods: ['PUT'])]
    public function updateProducts(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->isTokenValid($request)) {
            return $this->json(['error' => 'Invalid or missing token'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $content = json_decode($request->getContent(), true);

        if (is_null($content)) {
            return $this->json(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $response = [];

        foreach ($content as $productData) {
            if (!isset($productData['sku'])) {
                $response[] = ['error' => 'SKU is required', 'data' => $productData];
                continue;
            }

            $product = $entityManager->getRepository(Product::class)->findOneBy(['sku' => $productData['sku']]);

            if (!$product) {
                $response[] = ['error' => 'Product not found', 'sku' => $productData['sku']];
                continue;
            }

            $product->setProductName($productData['product_name'] ?? $product->getProductName());
            $product->setDescription($productData['description'] ?? $product->getDescription());

            $response[] = ['success' => 'Product updated successfully', 'sku' => $productData['sku']];
        }

        $entityManager->flush();

        return $this->json($response);
    }

    #[Route('/api/products', name: 'delete_products', methods: ['DELETE'])]
    public function deleteProducts(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->isTokenValid($request)) {
            return $this->json(['error' => 'Invalid or missing token'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $content = json_decode($request->getContent(), true);

        if (is_null($content)) {
            return $this->json(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $response = [];

        foreach ($content as $productData) {
            if (!isset($productData['sku'])) {
                $response[] = ['error' => 'SKU is required', 'data' => $productData];
                continue;
            }

            $product = $entityManager->getRepository(Product::class)->findOneBy(['sku' => $productData['sku']]);

            if (!$product) {
                $response[] = ['error' => 'Product not found', 'sku' => $productData['sku']];
                continue;
            }

            $entityManager->remove($product);

            $response[] = ['success' => 'Product deleted successfully', 'sku' => $productData['sku']];
        }

        $entityManager->flush();

        return $this->json($response);
    }

    private function isTokenValid(Request $request): bool
    {
        $token = $request->headers->get('X-AUTH-TOKEN');
        return $this->apiTokenService->isTokenValid($token);
    }
}
