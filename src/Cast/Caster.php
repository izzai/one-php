<?php

namespace Izzai\One\Cast;

/**
 * A utility class to contain the casting logic.
 */
class Caster
{
  private $primitives = ['string', 'int', 'float', 'bool', 'array', 'object', 'mixed', 'null', 'void'];

  /**
   * Casts generic data (array or object) into a target class, 
   * handling nested classes recursively by reading DocBlock annotations.
   * * @param object|array $data The raw data to cast.
   * @param string $className The fully qualified name of the target class.
   * @return object
   */
  public function castToClass(object|array $data, string $className): object
  {
    // 1. Initialize Reflection and target object
    $reflection = new \ReflectionClass($className);
    $obj = $reflection->newInstanceWithoutConstructor();

    // Convert input data to a standardized object for easy access
    $transformedData = json_decode(json_encode($data));

    if (!is_object($transformedData)) {
      // Handle case where input data is null or invalid after JSON conversion
      return $obj;
    }

    foreach ((array)$transformedData as $key => $value) {
      if (!$reflection->hasProperty($key) || $value === null) {
        continue;
      }

      $property = $reflection->getProperty($key);
      $propertyDocType = $this->getDocBlockType($property);

      // Check if it's a class we should attempt to cast recursively
      $isCustom = $this->isCustomClass($propertyDocType);
      if ($isCustom) {
        $propertyDocType = $this->namespaceClassName($propertyDocType);
        if (is_array($value)) {
          // 2. Handle array of nested objects (e.g., Message[])
          $nestedArray = [];
          foreach ($value as $item) {
            if (is_object($item) || is_array($item)) {
              $nestedArray[] = $this->castToClass($item, $propertyDocType);
            } else {
              // Handle primitive types mixed in a collection if necessary
              $nestedArray[] = $item;
            }
          }
          $obj->$key = $nestedArray;
        } elseif (is_object($value) || is_array($value)) {
          // 3. Handle single nested object (e.g., Agent)
          $obj->$key = $this->castToClass($value, $propertyDocType);
        } elseif ($this->hasBlockType($property, gettype($value))) {
          // 4. Custom, but also accepting primitives
          $obj->$key = $value;
        }
      } else {
        // 5. Default: Assign simple types
        $obj->$key = $value;
      }
    }
    return $obj;
  }

  protected function hasBlockType(\ReflectionProperty $property, string $type): bool
  {
    $docComment = $property->getDocComment();
    if ($docComment === false) {
      return false;
    }

    // Simple regex to find the type after @var
    if (preg_match('/@var\s+([a-zA-Z0-9\\\\\[\]_|()]+)/', $docComment, $matches)) {
      $types = array_values(
        array_map(
          fn($t) => strtolower($t),
          explode('|', str_replace(['(', ')', '[', ']'], '', $matches[1]))
        )
      );

      return in_array(strtolower($type), $types);
    }

    return false;
  }

  /**
   * Extracts the type from a property's @var DocBlock annotation.
   * Simplistic parser, assumes type is the first word after @var.
   * * @param \ReflectionProperty $property
   * @return string The type (e.g., 'string', 'int', '\App\Class', or '\App\Class[]').
   */
  protected function getDocBlockType(\ReflectionProperty $property): string
  {
    $docComment = $property->getDocComment();
    if ($docComment === false) {
      return '';
    }


    // Simple regex to find the type after @var
    if (preg_match('/@var\s+([a-zA-Z0-9\\\\\[\]_|()]+)/', $docComment, $matches)) {
      $types = explode('|', str_replace(['(', ')', '[', ']'], '', $matches[1]));
      $types = array_values(array_filter($types, fn($t) => !in_array(strtolower($t), $this->primitives)));

      return count($types) > 1 ? 'mixed' : ($types[0] ?? 'null');
    }

    return '';
  }

  /**
   * Checks if the given string is a valid, non-primitive class name.
   * * @param string $type
   * @return bool
   */
  protected function isCustomClass(string $type): bool
  {
    // Simple check to exclude common primitives and PHP built-in types
    if (in_array(strtolower($type), $this->primitives)) {
      return false;
    }

    // Check if the class exists and is a valid class/interface/trait
    $isCustom = class_exists($type) || interface_exists($type) || trait_exists($type);
    if (str_starts_with($type, 'Izzai\\One\\')) {
      return $isCustom;
    }

    return $isCustom || $this->isCustomClass(
      $this->namespaceClassName($type)
    );
  }

  private function namespaceClassName(string $name): string
  {
    if (str_starts_with($name, 'Izzai\\One\\')) {
      return $name;
    }

    return 'Izzai\\One\\Types\\' . ltrim($name, '\\');
  }
}
