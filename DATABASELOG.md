# Alterar CHARSET e COLLATE Banco de dados e tabelas individuais

## Siga os passo abaixo:

### Passo 1: Altera no Banco Geral

ALTER DATABASE 'db_name'
CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci

### Passo 2: Gera Listagem das tabelas com o ALTER TABLE para vc executar em seguida

SELECT
CONCAT('ALTER TABLE `', TABLE_NAME, '` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;')
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'db_name'

### Passo 3: Vai gerar uma listagem com os comandos para alterar em todas as tabelas (semelhante ex. abaixo), copie a saída e cole no linha de comando SQL e execute, para aplicar.

ALTER TABLE `ws_works_categories` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
ALTER TABLE `workcontrol_code` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
ALTER TABLE `ws_segments_images` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

### Passo:4 Por fim confira se todas as tabelas ficaram com o COLLATE desejado

SHOW TABLE STATUS

# ==========================================

# Alterar extensão de imagens no banco para .webp, primeira crie uma action no Photoshop para otimizar as imagens antes de mudar no banco.

## tabela ws_posts_images

### Valida antes de atualizar

SELECT
id,
image AS imagem_atual,
REGEXP_REPLACE(image, '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$', '.webp') AS nova_imagem
FROM `ws_posts_images` WHERE image IS NOT NULL
AND image <> ''
AND image REGEXP '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$';

### Aplica update na tabela

UPDATE ws_posts_images
SET image = REGEXP_REPLACE(image, '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$', '.webp')
WHERE image IS NOT NULL
AND image <> ''
AND image REGEXP '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$';

## tabela ws_posts

### Valida antes de atualizar

SELECT
post_id,
post_cover AS imagem_atual,
REGEXP_REPLACE(post_cover, '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$', '.webp') AS nova_imagem
FROM `ws_posts` WHERE post_cover IS NOT NULL
AND post_cover <> ''
AND post_cover REGEXP '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$';

### Aplica update na tabela

UPDATE 'ws_posts'
SET post_cover = REGEXP_REPLACE(post_cover, '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$', '.webp')
WHERE post_cover IS NOT NULL
AND post_cover <> ''
AND post_cover REGEXP '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$';

## tabela ws_users

### Valida antes de atualizar

SELECT
user_id,
user_thumb AS imagem_atual,
REGEXP_REPLACE(user_thumb, '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$', '.webp') AS nova_imagem
FROM `ws_users` WHERE user_thumb IS NOT NULL
AND user_thumb <> ''
AND user_thumb REGEXP '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$';

### Aplica update na tabela

UPDATE ws_users
SET user_thumb = REGEXP_REPLACE(user_thumb, '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$', '.webp')
WHERE user_thumb IS NOT NULL
AND user_thumb <> ''
AND user_thumb REGEXP '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$';

CREATE TABLE `portfolio` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`title` varchar(255) DEFAULT NULL,
`slug` varchar(255) DEFAULT NULL,
`description` text DEFAULT NULL,
`client` varchar(100) DEFAULT NULL,
`link_project` varchar(150) DEFAULT NULL,
`skills` varchar(200) DEFAULT NULL,
`key_metrics` varchar(200) DEFAULT NULL,
`measurement_period` varchar(200) DEFAULT NULL,
`problem` varchar(200) DEFAULT NULL,
`objectives` varchar(200) DEFAULT NULL,
`niche` varchar(200) DEFAULT NULL,
`project_duration` varchar(200) DEFAULT NULL,
`deliveryted_at` timestamp NULL DEFAULT NULL,
`category` smallint(6) DEFAULT NULL,
`author` int(11) unsigned NOT NULL,
`img_970x500` varchar(245) DEFAULT NULL,
`img_450x350` varchar(245) DEFAULT NULL,
`img_350x350` varchar(245) DEFAULT NULL,
`views` int(11) DEFAULT NULL,
`lastview` datetime DEFAULT NULL,
`show_client` tinyint(1) DEFAULT 1,
`highlight_case` tinyint(1) DEFAULT 0,
`status` tinyint(1) DEFAULT 0,
`created_at` timestamp NULL DEFAULT current_timestamp(),
`updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
PRIMARY KEY (`id`),
UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

## Modulo Servicos

```sql
CREATE TABLE IF NOT EXISTS `zv_services_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_parent` int(11) DEFAULT NULL,
  `category_title` varchar(255) DEFAULT NULL,
  `category_slug` varchar(255) DEFAULT NULL,
  `category_desc` varchar(155) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`category_id`),
  KEY `idx_services_categories_parent` (`category_parent`),
  UNIQUE KEY `uniq_services_categories_slug` (`category_slug`),
  CONSTRAINT `fk_services_categories_parent` FOREIGN KEY (`category_parent`) REFERENCES `zv_services_categories` (`category_id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `zv_services` (
  `svc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `svc_name` varchar(255) DEFAULT NULL,
  `svc_title` varchar(255) DEFAULT NULL,
  `svc_subtitle` varchar(155) DEFAULT NULL,
  `svc_description` longtext DEFAULT NULL,
  `svc_cover` varchar(255) DEFAULT NULL,
  `svc_icon` varchar(255) DEFAULT NULL,
  `svc_author` int(11) unsigned DEFAULT NULL,
  `svc_category` int(11) DEFAULT NULL,
  `svc_category_parent` varchar(255) DEFAULT NULL,
  `svc_views` decimal(10,0) DEFAULT 0,
  `svc_lastview` timestamp NULL DEFAULT NULL,
  `svc_status` tinyint(1) DEFAULT 0,
  `svc_created` timestamp NULL DEFAULT current_timestamp(),
  `svc_updated` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`svc_id`),
  UNIQUE KEY `uniq_services_name` (`svc_name`),
  KEY `idx_services_category` (`svc_category`),
  KEY `idx_services_author` (`svc_author`),
  CONSTRAINT `fk_services_author` FOREIGN KEY (`svc_author`) REFERENCES `ws_users` (`user_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk_services_category` FOREIGN KEY (`svc_category`) REFERENCES `zv_services_categories` (`category_id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `zv_services_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `svc_id` int(11) unsigned DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_services_images_svc_id` (`svc_id`),
  CONSTRAINT `fk_services_images_svc_id` FOREIGN KEY (`svc_id`) REFERENCES `zv_services` (`svc_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `zv_services_gallery` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `svc_id` int(11) unsigned DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_services_gallery_svc_id` (`svc_id`),
  CONSTRAINT `fk_services_gallery_svc_id` FOREIGN KEY (`svc_id`) REFERENCES `zv_services` (`svc_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
```

## Modulo Projetos

```sql
CREATE TABLE IF NOT EXISTS `zv_projects_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_parent` int(11) DEFAULT NULL,
  `category_title` varchar(255) DEFAULT NULL,
  `category_slug` varchar(255) DEFAULT NULL,
  `category_desc` varchar(155) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`category_id`),
  KEY `idx_projects_categories_parent` (`category_parent`),
  UNIQUE KEY `uniq_projects_categories_slug` (`category_slug`),
  CONSTRAINT `fk_projects_categories_parent` FOREIGN KEY (`category_parent`) REFERENCES `zv_projects_categories` (`category_id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `zv_projects` (
  `project_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_name` varchar(255) DEFAULT NULL,
  `project_title` varchar(255) DEFAULT NULL,
  `project_subtitle` varchar(155) DEFAULT NULL,
  `project_description` longtext DEFAULT NULL,
  `project_cover` varchar(255) DEFAULT NULL,
  `project_icon` varchar(255) DEFAULT NULL,
  `project_author` int(11) unsigned DEFAULT NULL,
  `project_category` int(11) DEFAULT NULL,
  `project_category_parent` varchar(255) DEFAULT NULL,
  `project_views` decimal(10,0) DEFAULT 0,
  `project_lastview` timestamp NULL DEFAULT NULL,
  `project_status` tinyint(1) DEFAULT 0,
  `project_created` timestamp NULL DEFAULT current_timestamp(),
  `project_updated` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`project_id`),
  UNIQUE KEY `uniq_projects_name` (`project_name`),
  KEY `idx_projects_category` (`project_category`),
  KEY `idx_projects_author` (`project_author`),
  CONSTRAINT `fk_projects_author` FOREIGN KEY (`project_author`) REFERENCES `ws_users` (`user_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk_projects_category` FOREIGN KEY (`project_category`) REFERENCES `zv_projects_categories` (`category_id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `zv_projects_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) unsigned DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_projects_images_project_id` (`project_id`),
  CONSTRAINT `fk_projects_images_project_id` FOREIGN KEY (`project_id`) REFERENCES `zv_projects` (`project_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `zv_projects_gallery` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) unsigned DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_projects_gallery_project_id` (`project_id`),
  CONSTRAINT `fk_projects_gallery_project_id` FOREIGN KEY (`project_id`) REFERENCES `zv_projects` (`project_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
```
