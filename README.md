# Introducción

Raptor PHP 3 aparece luciendo el nuevo look definido para las bases de los proyectos del programa 
de desarrollo, apareciendo en su descripción oficial como Raptor PHP 3. Esta nueva versión se enfoca 
en mejoras funcionales de la arquitectura planteda para Raptor 2 donde pueden apreciarse cambios en 
diferentes conceptos en el manejo del framework, aunque en general se mantiene la misma filosofía de trabajo.

Oficialmente Raptor PHP 3 pertenece al programa de desarrollo del Proyecto Raptor apoyado, por 
organizaciones e intituciones como la Unión de Informáticos de Cuba y los Joven Club de Computación y 
Electrónica.
 
## Estructura

La estructura del proyecto es bastante común, agrupando cada una de las partes fundamentales
 de un aplicación en directorios diferentes. En resumen encontraremos directorios con toda la 
configuración, bibliotecas del lado del servidor, código fuente los módulos(bundles) y el directorio
 de publicación de recursos web.

## Arquitectura

Raptor plantea una arquitectura basada en componentes, promoviendo la reutilización y buenas prácticas 
en el desarrollo.

La lógica de nuestro código estará dividida en módulos(bundles) que estarán a su vez agrupados por 
vendors, los vendors solo representarán un ente agrupador determinante solo de forma organizativa.

## Definición de Aspectos
La Programación Orientada a Aspectos(AOP) es una de las funcionalidades que se mantiene 
en esta versión y la lógica estará determinada por el framework Go! AOP.

Go! AOP permite resolver los problemas de la tradicional programación orientada a objetos, 
garantizando un eficiente sistema de intersección de funciones del código existente.
Características

    Intersección y ejecución de métodos public o protected en una clase
    Intersección y ejecución de métodos static o métodos en una clase final
    Intersección y ejecución de métodos en traits
    Intersección y acceso a propiedades public/protected en objetos
    Conector para inicialización de clases estáticas
    Conector para inicialización de objetos
    Intersección e invocación de funciones del sistema PHP
    Capacidad de cambiar los valores de retorno de métodos/funciones



## Instalación
Descargue las fuentes, después de solo debe correr el proyecto en:
	http://localhost/RaptorPHP3/web/dev.php

