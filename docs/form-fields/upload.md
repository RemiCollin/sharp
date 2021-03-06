# Form field: Upload

Class: `Code16\Sharp\Form\Fields\SharpFormUploadField`

## Configuration

### `setMaxFileSize(float $sizeInMB)`

Max file size allowed. 

### `setCropRatio(string $ratio)`

Set a ratio constraint to uploaded images, formatted like this: `width:height`. For instance: `16:9`, or `1:1`.

When a crop ratio is set, any uploaded picture will be auto-cropped (centered), and the "edit" tool will be accessible.

### `setStorageDisk(string $storageDisk)`

Set the destination storage disk (as configured in Laravel's  `config/filesystem.php` config file).

### `setStorageBasePath(string $storageBasePath)`

Set the destination base storage path.

### `setFileFilter($fileFilter)`

Set the allowed file extensions. You can pass either an array or a comma-separated list.

### `setFileFilterImages()`

Just a `setFileFilter([".jpg",".jpeg",".gif",".png"])` shorthand.


## Formatter

This part is more complex for this field than others... 

First, let's mention that Sharp provides an Eloquent built-in solution for uploads with the `SharpUploadModel` class, as [detailed here](../sharp-built-in-solution-for-uploads.md), which greatly simplify the work. 

Here's the documentation for the non built-in solution:


### `toFront`

The front expects an array with 3 keys:

    [
        "name" => "", // Relative file path
        "thumbnail" => "", // 150px height thumbnail full url
        "size" => x, // Size in bytes
    ]

The formatter can't handle it automatically, it too project-sepecific. You'll have to provide this in a custom transformer ([see full documentation](how-to-transform-data.md)) like this:

    function find($id): array
    {
        return $this->setCustomTransformer("picture", 
            function($value, $spaceship, $attribute) {
                return [
				    "name" => $spaceship->picture->name,
				    "thumbnail" => [...],
				    "size" => $spaceship->picture->size,
                ];
            })
            ->transform(
                Spaceship::findOrFail($id)
            );
    }

### `fromFront`

There are four cases:

- newly uploaded file

The formatter will perform any transformation, store the file on the configured location, and return an array like this:

    [
        "file_name" => "", // Relative file path
        "size" => x, // File size in bytes
        "mime_type" => "", // File mime type
        "disk" => "", // Storage disk name
        "transformed" => true // True if the file was transformed
    ];

It's up to you then to store any of these values in a DB or elsewhere.

Using the `Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater`, you will probably reach a solution like this:

    function update($id, array $data)
    {
        $instance = $id ? Spaceship::findOrFail($id) : new Spaceship;

        $this->ignore("picture")->save($instance, $data);
        
        // Then handle $data["picture"] here
    }


- existing transformed file 

In this case, the file was already stored, but was transformed (cropped, or rotated). The formatter will transform the file, store the result and simply return and array with one key:

    [
        "transformed" => true
    ];

Then you'll have to catch that if needed (to destroy all previous generated thumbnails for instance).


- deleted file

The formatter will return null (without deleting the file from the storage).


- existing and unchanged file

The formatter will return an empty array.