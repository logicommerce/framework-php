<?php

namespace FWK\Core\Dtos\Factories;

use SDK\Core\Dtos\Factories\TransactionDocumentRowFactory as SDKDocumentRowFactory;

/**
 * This class will return the kind of Document row we need.
 *
 * @see SDK\Core\Dtos\Factories\TransactionDocumentRowFactory
 *
 * @package FWK\Dtos\Factories
 */
abstract class DocumentRowFactory extends SDKDocumentRowFactory {

    protected const NAMESPACE = 'FWK\Dtos\Documents\DocumentRow';

}
