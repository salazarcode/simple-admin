# Usamos la imagen de Bitnami para Laravel como base
FROM bitnami/laravel:latest

# ---- INICIO DE LA SECCIÓN DE MODIFICACIONES ----

# Cambiamos al usuario 'root' para poder instalar software nuevo
USER root

# Instalamos dependencias necesarias para la aplicación Laravel}
RUN apt-get update && apt-get install -y default-mysql-client curl

# Instalamos una versión moderna de Node.js y NPM usando NodeSource (recomendado)
# 1. Descargamos y ejecutamos el script de configuración para Node.js versión 20.x
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
# 2. Instalamos el paquete 'nodejs', que incluye tanto 'node' como 'npm'
RUN apt-get install -y nodejs

# Limpiamos la caché de 'apt' para mantener el tamaño de la imagen optimizado
RUN rm -rf /var/lib/apt/lists/*

# ---- FIN DE LA SECCIÓN DE MODIFICACIONES ----

# Copiamos tu script de entrada personalizado
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
# Le damos permisos de ejecución
RUN chmod +x /usr/local/bin/entrypoint.sh

# Regresamos al usuario no-root 'bitnami' por seguridad
USER 1001

# Establecemos nuestro script como el punto de entrada principal del contenedor
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]