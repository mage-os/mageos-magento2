# Magento_MediaGalleryApi module

This module serves as application program interface (API) for storing and managing media gallery asset attributes.

## Installation details

For information about module installation, see [Enable or disable modules](https://experienceleague.adobe.com/en/docs/commerce-operations/installation-guide/tutorials/manage-modules).

## Extensibility

Extension developers can interact with the Magento_MediaGalleryApi module. For more information about the extension mechanism, see [Plugins](https://developer.adobe.com/commerce/php/development/components/plugins/).

[The dependency injection mechanism](https://developer.adobe.com/commerce/php/development/components/dependency-injection/) enables you to override the functionality of the Magento_MediaGalleryApi module.

### Public APIs

- `\Magento\MediaGalleryApi\Api\Data\AssetInterface`
    - media asset entity data

- `\Magento\MediaGalleryApi\Api\Data\AssetKeywordsInterface`
    - assets keywords aggregation

- `\Magento\MediaGalleryApi\Api\Data\AssetKeywordsInterface`
    - media asset keyword entity data

- `\Magento\MediaGalleryApi\Api\CreateDirectoriesByPathsInterface`:
    - create new directories by provided paths

- `\Magento\MediaGalleryApi\Api\DeleteAssetsByPathsInterface`:
    - delete media assets by paths. Removes all the assets which paths start with provided paths

- `\Magento\MediaGalleryApi\Api\DeleteDirectoriesByPathsInterface`:
    - delete folders by provided paths

- `\Magento\MediaGalleryApi\Api\GetAssetsByIdsInterface`:
    - get media gallery assets by id attribute

- `\Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface`:
    - get media gallery assets by paths in media storage

- `\Magento\MediaGalleryApi\Api\GetAssetsKeywordsInterface`:
    - get a media gallery asset keywords related to media gallery asset ids provided

- `\Magento\MediaGalleryApi\Api\IsPathExcludedInterface`:
    - check if the path is excluded from displaying and processing in the media gallery

- `\Magento\MediaGalleryApi\Api\SaveAssetsInterface`:
    - save media gallery assets to the database

- `\Magento\MediaGalleryApi\Api\SaveAssetsKeywordsInterface`:
    - save keywords related to assets to the database
  
- `\Magento\MediaGalleryApi\Api\SearchAssetsInterface`:
    - search media gallery assets

For information about a public API, see [Public interfaces & APIs](https://developer.adobe.com/commerce/php/development/components/api-concepts/).

## Additional information

For information about significant changes in patch releases, see [Release information](https://experienceleague.adobe.com/en/docs/commerce-operations/release/notes/overview).
