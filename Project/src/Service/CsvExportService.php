<?php

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportService
{
    public function exportProducts(array $products): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($products) {
            $handle = fopen('php://output', 'w+');
            
            // Add BOM for Excel compatibility
            fputs($handle, "\xEF\xBB\xBF");

            // Header
            fputcsv($handle, ['Name', 'Description', 'Price'], ';');

            foreach ($products as $product) {
                if ($product instanceof Product) {
                    fputcsv($handle, [
                        $product->getName(),
                        $product->getDescription(),
                        $product->getPrice()
                    ], ';');
                }
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="products.csv"');

        return $response;
    }
}
