<?php

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Documentación de la API del Clima",
 *     description="API para obtener información meteorológica y gestionar preferencias de usuario",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * )
 *
 * @OA\Server(
 *     description="Servidor Local",
 *     url="http://localhost:8000"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
