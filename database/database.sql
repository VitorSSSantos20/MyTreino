CREATE DATABASE IF NOT EXISTS mytreino
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE mytreino;

-- ------------------------------------------------------------
-- Tabela de usuários
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
  id         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  nome       VARCHAR(100)     NOT NULL,
  email      VARCHAR(150)     NOT NULL,
  senha      VARCHAR(255)     NOT NULL,           
  peso       DECIMAL(5,2)     NULL,               
  altura     SMALLINT UNSIGNED NULL,              
  idade      TINYINT UNSIGNED NULL,
  created_at TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_usuarios_email (email)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tabela de treinos 
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS treinos (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario_id     INT UNSIGNED NOT NULL,
  dia_semana     TINYINT UNSIGNED NOT NULL,       
  tipo_dia       ENUM('treino','descanso') NOT NULL DEFAULT 'treino',
  nome_treino    VARCHAR(100) NULL,               
  grupo_muscular VARCHAR(100) NULL,               
  observacao     VARCHAR(255) NULL,
  created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_treinos_usuario_dia (usuario_id, dia_semana),
  CONSTRAINT fk_treinos_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tabela de exercícios
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS exercicios (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  treino_id  INT UNSIGNED NOT NULL,
  nome       VARCHAR(100) NOT NULL,               
  series     TINYINT UNSIGNED NOT NULL,           
  repeticoes VARCHAR(20)  NOT NULL,               
  observacao VARCHAR(255) NULL,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_exercicios_treino
    FOREIGN KEY (treino_id) REFERENCES treinos (id)
    ON DELETE CASCADE
) ENGINE=InnoDB;
