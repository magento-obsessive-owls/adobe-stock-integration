<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeMediaGalleryApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Asset Interface
 *
 * @api
 */
interface AssetInterface extends ExtensibleDataInterface
{
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Get Path
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Get content type
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * Retrieve full licensed asset's height
     *
     * @return int
     */
    public function getHeight(): int;

    /**
     * Retrieve full licensed asset's width
     *
     * @return int
     */
    public function getWidth(): int;

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\AssetExtensionInterface|null
     */
    public function getExtensionAttributes(): AssetExtensionInterface;

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\AssetExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(AssetExtensionInterface $extensionAttributes): void;
}
