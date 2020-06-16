# Wwwision.Form.SecureFileUpload

Flow package with examples and helpers to implement secure form uploads

## Description

When using the default `FileUpload` element of the [Flow Form Framework](https://github.com/neos/form) instances of
`PersistentResource` are created in the default (`persistent`) resource collection that creates publicly accessible
URLs for each resource.
This package configures all `FileUpload` fields to use a new resource collection `formUploads` that uses "private resources"
provided by the [wwwision/privateresources](https://github.com/bwaidelich/Wwwision.PrivateResources) package.

This also contains a Form finisher that removes uploaded resources once they have been processed or sent via email for example.

## Installation

    composer require wwwision/form-securefileupload

## Usage

By installing this package, files that are uploaded via the `FileUpload` form element are automatically stored
in the protected `formUploads` resource collection.

### Cleanup uploaded files

With this package, uploaded files are no longer accessible from the "outside" without a valid token (see https://github.com/bwaidelich/Wwwision.PrivateResources).
However, they are still persisted in the servers filesystem (by default) and referenced via the `neos_flow_resourcemanagement_persistentresource`
database table.

In order to remove uploaded files after they have been processed, the provided `RemoveUploads` finisher can be used.

From the form definition (yaml):

```yaml
type: 'Neos.Form:Form'
identifier: 'contact'
label: 'Contact form'
renderables:
  -
    type: 'Neos.Form:Page'
    identifier: 'page-one'
    # renderables: ...
finishers:
  -
    # process uploads (e.g. send via email)
    identifier: 'Neos.Form:Email'
    options:
      attachAllPersistentResources: true
      # ...
  -
    # delete persistent resources
    identifier: 'Wwwision.Form.SecureFileUpload:RemoveUploadsFinisher'
```

*Note:* In the current implementation the `RemoveUploads` finisher iterates over all form elements and deletes all instances of `PersistentResource`.
For a more fine granular behavior you should create a custom finisher (or send a pull request my way *g)

### Form Builder

This package can be used with the [neos/form-builder](https://github.com/neos/form-builder) package of course.
In order to be able to attach the finisher from the backend, a corresponding Node Type and fusion definition is required:

*NodeTypes.yaml:*

```yaml
'Your.Package:RemoveUploadsFinisher':
  superTypes:
    'Neos.Form.Builder:AbstractFinisher': true
  ui:
    label: 'Remove Uploads Finisher'
    icon: 'icon-trash'

```

*root.fusion*:

```neosfusion
prototype(Your.Package:RemoveUploadsFinisher.Definition) < prototype(Neos.Form.Builder:Finisher.Definition) {
    formElementType = 'Wwwision.Form.SecureFileUpload:Finisher.RemoveUploads'
}
```
