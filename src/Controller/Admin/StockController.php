<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/stock')]
#[IsGranted('ROLE_EMPLOYEE')]
class StockController extends AbstractController
{
    #[Route('/', name: 'admin_stock')]
    public function index(
        ProductRepository $productRepo,
        ProductCategoryRepository $categoryRepo
    ): Response {
        $products = $productRepo->findBy([], ['name' => 'ASC']);
        $categories = $categoryRepo->findAll();
        
        // Calculer les stats
        $lowStock = count(array_filter($products, fn($p) => $p->getQuantity() < 10));
        $outOfStock = count(array_filter($products, fn($p) => $p->getQuantity() == 0));
        
        return $this->render('admin/stock/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
        ]);
    }

    #[Route('/product/add', name: 'admin_stock_product_add', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function addProduct(
        Request $request,
        EntityManagerInterface $em,
        ProductCategoryRepository $categoryRepo
    ): Response {
        $product = new Product();
        $product->setName($request->request->get('name'));
        $product->setDescription($request->request->get('description'));
        $product->setQuantity((int) $request->request->get('quantity'));
        $product->setPrice((float) $request->request->get('price'));
        
        $categoryId = $request->request->get('category');
        if ($categoryId) {
            $category = $categoryRepo->find($categoryId);
            if ($category) {
                $product->setCategory($category);
            }
        }
        
        $em->persist($product);
        $em->flush();
        
        $this->addFlash('success', 'Produit ajouté avec succès !');
        return $this->redirectToRoute('admin_stock');
    }

    #[Route('/product/{id}/update-quantity', name: 'admin_stock_update_quantity', methods: ['POST'])]
    public function updateQuantity(
        int $id,
        Request $request,
        ProductRepository $repository,
        EntityManagerInterface $em
    ): Response {
        $product = $repository->find($id);
        
        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }
        
        $newQuantity = (int) $request->request->get('quantity');
        $product->setQuantity($newQuantity);
        $em->flush();
        
        $this->addFlash('success', 'Stock mis à jour !');
        return $this->redirectToRoute('admin_stock');
    }

    #[Route('/product/{id}/delete', name: 'admin_stock_product_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteProduct(
        int $id,
        ProductRepository $repository,
        EntityManagerInterface $em
    ): Response {
        $product = $repository->find($id);
        
        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }
        
        $em->remove($product);
        $em->flush();
        
        $this->addFlash('success', 'Produit supprimé !');
        return $this->redirectToRoute('admin_stock');
    }
    
    #[Route('/category/add', name: 'admin_stock_category_add', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function addCategory(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $category = new ProductCategory();
        $category->setName($request->request->get('category_name'));
        $category->setDescription($request->request->get('category_description'));
        
        $em->persist($category);
        $em->flush();
        
        $this->addFlash('success', 'Catégorie ajoutée avec succès !');
        return $this->redirectToRoute('admin_stock');
    }
}
