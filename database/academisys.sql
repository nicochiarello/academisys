-- =============================================================================
-- AcademiSys — Script de base de datos completo
-- Plataforma de gestión académica universitaria
-- Motor: InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------------------------------
-- Crear y seleccionar la base de datos
-- -----------------------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS academisys
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE academisys;

-- =============================================================================
-- ELIMINACIÓN DE OBJETOS EXISTENTES (orden inverso a las dependencias FK)
-- =============================================================================

-- Vistas
DROP VIEW IF EXISTS v_estado_plan_carrera;
DROP VIEW IF EXISTS v_asistencia_por_inscripcion;
DROP VIEW IF EXISTS v_promedio_por_alumno;
DROP VIEW IF EXISTS v_historial_alumno;
DROP VIEW IF EXISTS v_comisiones_activas;

-- Triggers
DROP TRIGGER IF EXISTS trg_after_insert_nota;
DROP TRIGGER IF EXISTS trg_after_update_ciclo;
DROP TRIGGER IF EXISTS trg_before_insert_comision;
DROP TRIGGER IF EXISTS trg_after_insert_inscripcion;

-- Stored Procedures
DROP PROCEDURE IF EXISTS OtorgarTitulo;
DROP PROCEDURE IF EXISTS RegistrarAsistencia;
DROP PROCEDURE IF EXISTS RegistrarNota;
DROP PROCEDURE IF EXISTS InscribirAlumno;

-- Tablas (orden inverso a la creación para respetar las FK)
DROP TABLE IF EXISTS Log_Inscripciones;
DROP TABLE IF EXISTS Titulo_Obtenido;
DROP TABLE IF EXISTS Asistencia;
DROP TABLE IF EXISTS Nota;
DROP TABLE IF EXISTS Inscripcion;
DROP TABLE IF EXISTS Comision;
DROP TABLE IF EXISTS Usuario;
DROP TABLE IF EXISTS Alumno;
DROP TABLE IF EXISTS Profesor;
DROP TABLE IF EXISTS Aula;
DROP TABLE IF EXISTS Ciclo_Academico;
DROP TABLE IF EXISTS Correlativa;
DROP TABLE IF EXISTS Materia;
DROP TABLE IF EXISTS Plan;
DROP TABLE IF EXISTS Carrera;
DROP TABLE IF EXISTS Rol;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- CREACIÓN DE TABLAS
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1. Rol — Tipos de usuarios del sistema
-- -----------------------------------------------------------------------------
CREATE TABLE Rol (
  id_rol  INT          NOT NULL AUTO_INCREMENT,
  nombre  VARCHAR(60)  NOT NULL,
  PRIMARY KEY (id_rol),
  UNIQUE KEY uq_rol_nombre (nombre)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Roles del sistema (Administrador, Bedel, Docente, Alumno)';

-- -----------------------------------------------------------------------------
-- 2. Carrera — Carreras universitarias ofrecidas
-- -----------------------------------------------------------------------------
CREATE TABLE Carrera (
  id_carrera      INT          NOT NULL AUTO_INCREMENT,
  nombre          VARCHAR(100) NOT NULL,
  duracion_años   INT          NOT NULL,
  titulo_otorga   VARCHAR(100),
  modalidad       ENUM('presencial','semipresencial','virtual') NOT NULL DEFAULT 'presencial',
  activa          TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (id_carrera)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Carreras universitarias';

-- -----------------------------------------------------------------------------
-- 3. Plan — Plan de estudios de cada carrera
-- -----------------------------------------------------------------------------
CREATE TABLE Plan (
  id_plan       INT          NOT NULL AUTO_INCREMENT,
  id_carrera    INT          NOT NULL,
  año_vigencia  INT          NOT NULL,
  descripcion   VARCHAR(100),
  PRIMARY KEY (id_plan),
  CONSTRAINT fk_plan_carrera FOREIGN KEY (id_carrera)
    REFERENCES Carrera (id_carrera)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Planes de estudio por carrera y año de vigencia';

-- -----------------------------------------------------------------------------
-- 4. Materia — Materias de cada plan de estudios
-- -----------------------------------------------------------------------------
CREATE TABLE Materia (
  id_materia    INT         NOT NULL AUTO_INCREMENT,
  id_plan       INT         NOT NULL,
  nombre        VARCHAR(100) NOT NULL,
  codigo        VARCHAR(20)  NOT NULL,
  año_cursada   INT          NOT NULL,
  cuatrimestre  INT          NOT NULL,
  carga_horaria INT          NOT NULL,
  PRIMARY KEY (id_materia),
  UNIQUE KEY uq_materia_codigo (codigo),
  CONSTRAINT fk_materia_plan FOREIGN KEY (id_plan)
    REFERENCES Plan (id_plan)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Materias por plan de estudios';

-- -----------------------------------------------------------------------------
-- 5. Correlativa — Correlatividades entre materias
-- -----------------------------------------------------------------------------
CREATE TABLE Correlativa (
  id_materia      INT NOT NULL,
  id_correlativa  INT NOT NULL,
  PRIMARY KEY (id_materia, id_correlativa),
  CONSTRAINT fk_corr_materia FOREIGN KEY (id_materia)
    REFERENCES Materia (id_materia)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_corr_prereq FOREIGN KEY (id_correlativa)
    REFERENCES Materia (id_materia)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Correlatividades: id_materia requiere tener aprobada id_correlativa';

-- -----------------------------------------------------------------------------
-- 6. Ciclo_Academico — Ciclos lectivos (cuatrimestres)
-- -----------------------------------------------------------------------------
CREATE TABLE Ciclo_Academico (
  id_ciclo      INT        NOT NULL AUTO_INCREMENT,
  anio          INT        NOT NULL,
  cuatrimestre  INT        NOT NULL,
  fecha_inicio  DATE,
  fecha_fin     DATE,
  activo        TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id_ciclo),
  UNIQUE KEY uq_ciclo (anio, cuatrimestre),
  CONSTRAINT chk_ciclo_cuatrimestre CHECK (cuatrimestre IN (1, 2))
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Ciclos académicos (cuatrimestres). Solo uno puede estar activo a la vez';

-- -----------------------------------------------------------------------------
-- 7. Aula — Aulas y laboratorios disponibles
-- -----------------------------------------------------------------------------
CREATE TABLE Aula (
  id_aula      INT         NOT NULL AUTO_INCREMENT,
  numero       VARCHAR(10) NOT NULL,
  edificio     VARCHAR(50),
  capacidad    INT,
  equipamiento VARCHAR(100),
  PRIMARY KEY (id_aula)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Aulas y espacios físicos de cursada';

-- -----------------------------------------------------------------------------
-- 8. Profesor — Docentes de la institución
-- -----------------------------------------------------------------------------
CREATE TABLE Profesor (
  id_profesor   INT          NOT NULL AUTO_INCREMENT,
  dni           VARCHAR(15)  NOT NULL,
  apellido      VARCHAR(60)  NOT NULL,
  nombre        VARCHAR(60)  NOT NULL,
  email         VARCHAR(150) NOT NULL,
  telefono      VARCHAR(30),
  fecha_ingreso DATE,
  activo        TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (id_profesor),
  UNIQUE KEY uq_profesor_dni   (dni),
  UNIQUE KEY uq_profesor_email (email)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Docentes de la institución';

-- -----------------------------------------------------------------------------
-- 9. Alumno — Estudiantes de la institución
-- -----------------------------------------------------------------------------
CREATE TABLE Alumno (
  legajo          VARCHAR(20)  NOT NULL,
  dni             VARCHAR(15)  NOT NULL,
  apellido        VARCHAR(60)  NOT NULL,
  nombre          VARCHAR(60)  NOT NULL,
  email           VARCHAR(150) NOT NULL,
  telefono        VARCHAR(30),
  fecha_nacimiento DATE,
  fecha_ingreso   DATE,
  id_carrera      INT          NOT NULL,
  activo          TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (legajo),
  UNIQUE KEY uq_alumno_dni   (dni),
  UNIQUE KEY uq_alumno_email (email),
  CONSTRAINT fk_alumno_carrera FOREIGN KEY (id_carrera)
    REFERENCES Carrera (id_carrera)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Alumnos de la institución';

-- -----------------------------------------------------------------------------
-- 10. Usuario — Cuentas de acceso al sistema
-- -----------------------------------------------------------------------------
CREATE TABLE Usuario (
  id_usuario     INT          NOT NULL AUTO_INCREMENT,
  email          VARCHAR(150) NOT NULL,
  password_hash  VARCHAR(255) NOT NULL,
  id_rol         INT          NOT NULL,
  legajo_alumno  VARCHAR(20)  NULL DEFAULT NULL,
  id_profesor    INT          NULL DEFAULT NULL,
  activo         TINYINT(1)   NOT NULL DEFAULT 1,
  created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_usuario),
  UNIQUE KEY uq_usuario_email (email),
  CONSTRAINT fk_usuario_rol      FOREIGN KEY (id_rol)
    REFERENCES Rol (id_rol)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_usuario_alumno   FOREIGN KEY (legajo_alumno)
    REFERENCES Alumno (legajo)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_usuario_profesor FOREIGN KEY (id_profesor)
    REFERENCES Profesor (id_profesor)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Usuarios del sistema con su rol y referencia a alumno o profesor';

-- -----------------------------------------------------------------------------
-- 11. Comision — Comisiones (cursos) de cada materia por ciclo
-- -----------------------------------------------------------------------------
CREATE TABLE Comision (
  id_comision  INT        NOT NULL AUTO_INCREMENT,
  id_materia   INT        NOT NULL,
  id_profesor  INT        NOT NULL,
  id_aula      INT        NOT NULL,
  id_ciclo     INT        NOT NULL,
  turno        ENUM('mañana','tarde','noche'),
  cupo_maximo  INT,
  PRIMARY KEY (id_comision),
  CONSTRAINT fk_comision_materia  FOREIGN KEY (id_materia)
    REFERENCES Materia (id_materia)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_comision_profesor FOREIGN KEY (id_profesor)
    REFERENCES Profesor (id_profesor)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_comision_aula     FOREIGN KEY (id_aula)
    REFERENCES Aula (id_aula)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_comision_ciclo    FOREIGN KEY (id_ciclo)
    REFERENCES Ciclo_Academico (id_ciclo)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Comisiones de cursada (materia + docente + aula + ciclo)';

-- -----------------------------------------------------------------------------
-- 12. Inscripcion — Inscripciones de alumnos a comisiones
-- -----------------------------------------------------------------------------
CREATE TABLE Inscripcion (
  id_inscripcion    INT         NOT NULL AUTO_INCREMENT,
  id_alumno         VARCHAR(20) NOT NULL,
  id_comision       INT         NOT NULL,
  fecha_inscripcion DATE,
  estado            ENUM('activa','baja','aprobada','desaprobada') NOT NULL DEFAULT 'activa',
  PRIMARY KEY (id_inscripcion),
  CONSTRAINT fk_inscripcion_alumno   FOREIGN KEY (id_alumno)
    REFERENCES Alumno (legajo)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_inscripcion_comision FOREIGN KEY (id_comision)
    REFERENCES Comision (id_comision)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Inscripciones de alumnos a comisiones de cursada';

-- -----------------------------------------------------------------------------
-- 13. Nota — Calificaciones por inscripción
-- -----------------------------------------------------------------------------
CREATE TABLE Nota (
  id_nota        INT            NOT NULL AUTO_INCREMENT,
  id_inscripcion INT            NOT NULL,
  tipo           ENUM('parcial_1','parcial_2','recuperatorio','final','coloquio'),
  fecha          DATE,
  calificacion   DECIMAL(4,2),
  observacion    VARCHAR(100),
  PRIMARY KEY (id_nota),
  CONSTRAINT fk_nota_inscripcion FOREIGN KEY (id_inscripcion)
    REFERENCES Inscripcion (id_inscripcion)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Notas/calificaciones por inscripción y tipo de evaluación';

-- -----------------------------------------------------------------------------
-- 14. Asistencia — Registro de asistencia por clase
-- -----------------------------------------------------------------------------
CREATE TABLE Asistencia (
  id_asistencia  INT        NOT NULL AUTO_INCREMENT,
  id_inscripcion INT        NOT NULL,
  fecha_clase    DATE       NOT NULL,
  presente       TINYINT(1) NOT NULL DEFAULT 0,
  justificada    TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id_asistencia),
  UNIQUE KEY uq_asist (id_inscripcion, fecha_clase),
  CONSTRAINT fk_asistencia_inscripcion FOREIGN KEY (id_inscripcion)
    REFERENCES Inscripcion (id_inscripcion)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Registro de asistencia por alumno, inscripción y fecha de clase';

-- -----------------------------------------------------------------------------
-- 15. Titulo_Obtenido — Títulos emitidos a alumnos egresados
-- -----------------------------------------------------------------------------
CREATE TABLE Titulo_Obtenido (
  id_titulo       INT            NOT NULL AUTO_INCREMENT,
  id_alumno       VARCHAR(20)    NOT NULL,
  id_carrera      INT            NOT NULL,
  id_plan         INT            NOT NULL,
  fecha_egreso    DATE,
  promedio_final  DECIMAL(4,2),
  PRIMARY KEY (id_titulo),
  CONSTRAINT fk_titulo_alumno  FOREIGN KEY (id_alumno)
    REFERENCES Alumno (legajo)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_titulo_carrera FOREIGN KEY (id_carrera)
    REFERENCES Carrera (id_carrera)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_titulo_plan    FOREIGN KEY (id_plan)
    REFERENCES Plan (id_plan)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Títulos otorgados a alumnos que completaron el plan de estudios';

-- -----------------------------------------------------------------------------
-- 16. Log_Inscripciones — Auditoría de operaciones de inscripción
-- Sin FK para no bloquear las operaciones auditadas
-- -----------------------------------------------------------------------------
CREATE TABLE Log_Inscripciones (
  id_log         INT         NOT NULL AUTO_INCREMENT,
  id_inscripcion INT         NOT NULL,
  legajo_alumno  VARCHAR(20),
  id_comision    INT,
  accion         VARCHAR(50),
  fecha_evento   DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_usuario     INT         NULL DEFAULT NULL,
  PRIMARY KEY (id_log)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Log de auditoría de inscripciones (sin FK para no bloquear operaciones)';

-- =============================================================================
-- STORED PROCEDURES
-- =============================================================================

DELIMITER $$

-- -----------------------------------------------------------------------------
-- SP1 — InscribirAlumno
-- Inscribe un alumno a una comisión verificando duplicados, cupo y correlativas
-- -----------------------------------------------------------------------------
CREATE PROCEDURE InscribirAlumno(
  IN p_legajo      VARCHAR(20),
  IN p_id_comision INT
)
BEGIN
  DECLARE v_id_materia    INT;
  DECLARE v_cupo_max      INT;
  DECLARE v_inscriptos    INT;
  DECLARE v_ya_inscripto  INT;
  DECLARE v_falta_corr    INT;

  proc: BEGIN

    -- 1. Verificar que el alumno no tenga inscripción vigente en esta comisión
    SELECT COUNT(*) INTO v_ya_inscripto
    FROM Inscripcion
    WHERE id_alumno   = p_legajo
      AND id_comision = p_id_comision
      AND estado IN ('activa', 'aprobada', 'desaprobada');

    IF v_ya_inscripto > 0 THEN
      SELECT 'ERROR: El alumno ya está inscripto en esta comisión' AS mensaje;
      LEAVE proc;
    END IF;

    -- 2. Verificar que la comisión no haya superado el cupo máximo
    SELECT cupo_maximo INTO v_cupo_max
    FROM Comision
    WHERE id_comision = p_id_comision;

    SELECT COUNT(*) INTO v_inscriptos
    FROM Inscripcion
    WHERE id_comision = p_id_comision
      AND estado IN ('activa', 'aprobada');

    IF v_inscriptos >= v_cupo_max THEN
      SELECT 'ERROR: La comisión alcanzó su cupo máximo' AS mensaje;
      LEAVE proc;
    END IF;

    -- Obtener la materia asociada a la comisión
    SELECT id_materia INTO v_id_materia
    FROM Comision
    WHERE id_comision = p_id_comision;

    -- 3. Verificar correlativas: cada materia requerida debe estar aprobada
    SELECT COUNT(*) INTO v_falta_corr
    FROM Correlativa CR
    WHERE CR.id_materia = v_id_materia
      AND NOT EXISTS (
        SELECT 1
        FROM Inscripcion I
        JOIN Comision    C ON C.id_comision = I.id_comision
        WHERE I.id_alumno   = p_legajo
          AND I.estado      = 'aprobada'
          AND C.id_materia  = CR.id_correlativa
      );

    IF v_falta_corr > 0 THEN
      SELECT 'ERROR: El alumno no cumple las correlativas requeridas' AS mensaje;
      LEAVE proc;
    END IF;

    -- 4. Realizar la inscripción
    INSERT INTO Inscripcion (id_alumno, id_comision, fecha_inscripcion, estado)
    VALUES (p_legajo, p_id_comision, CURDATE(), 'activa');

    -- 5. Confirmar operación exitosa
    SELECT 'OK: Inscripción realizada correctamente' AS mensaje;

  END; -- fin proc
END$$

-- -----------------------------------------------------------------------------
-- SP2 — RegistrarNota
-- Inserta o actualiza una nota para una inscripción activa
-- -----------------------------------------------------------------------------
CREATE PROCEDURE RegistrarNota(
  IN p_id_inscripcion INT,
  IN p_tipo           VARCHAR(20),
  IN p_calificacion   DECIMAL(4,2),
  IN p_fecha          DATE,
  IN p_observacion    VARCHAR(100)
)
BEGIN
  DECLARE v_estado_inscripcion VARCHAR(20);
  DECLARE v_nota_existente     INT;

  proc: BEGIN

    -- 1. Verificar que la inscripción existe y está activa
    SELECT estado INTO v_estado_inscripcion
    FROM Inscripcion
    WHERE id_inscripcion = p_id_inscripcion;

    IF v_estado_inscripcion IS NULL OR v_estado_inscripcion != 'activa' THEN
      SELECT 'ERROR: Inscripción no válida' AS mensaje;
      LEAVE proc;
    END IF;

    -- 2. Verificar si ya existe una nota del mismo tipo para esa inscripción
    SELECT COUNT(*) INTO v_nota_existente
    FROM Nota
    WHERE id_inscripcion = p_id_inscripcion
      AND tipo           = p_tipo;

    IF v_nota_existente > 0 THEN
      -- Actualizar nota existente
      UPDATE Nota
      SET calificacion = p_calificacion,
          fecha        = p_fecha,
          observacion  = p_observacion
      WHERE id_inscripcion = p_id_inscripcion
        AND tipo           = p_tipo;
    ELSE
      -- 3. Insertar nueva nota
      INSERT INTO Nota (id_inscripcion, tipo, fecha, calificacion, observacion)
      VALUES (p_id_inscripcion, p_tipo, p_fecha, p_calificacion, p_observacion);
    END IF;

    -- 4. Confirmar operación exitosa
    SELECT 'OK: Nota registrada correctamente' AS mensaje;

  END; -- fin proc
END$$

-- -----------------------------------------------------------------------------
-- SP3 — RegistrarAsistencia
-- Registra o actualiza la asistencia de un alumno a una clase
-- -----------------------------------------------------------------------------
CREATE PROCEDURE RegistrarAsistencia(
  IN p_id_inscripcion INT,
  IN p_fecha_clase    DATE,
  IN p_presente       TINYINT,
  IN p_justificada    TINYINT
)
BEGIN
  -- Upsert: inserta o actualiza si ya existe el registro para esa fecha
  INSERT INTO Asistencia (id_inscripcion, fecha_clase, presente, justificada)
  VALUES (p_id_inscripcion, p_fecha_clase, p_presente, p_justificada)
  ON DUPLICATE KEY UPDATE
    presente   = VALUES(presente),
    justificada = VALUES(justificada);

  SELECT 'OK: Asistencia registrada' AS mensaje;
END$$

-- -----------------------------------------------------------------------------
-- SP4 — OtorgarTitulo
-- Verifica que el alumno haya aprobado todas las materias del plan y otorga el título
-- -----------------------------------------------------------------------------
CREATE PROCEDURE OtorgarTitulo(
  IN p_legajo  VARCHAR(20),
  IN p_id_plan INT
)
BEGIN
  DECLARE v_total_materias INT;
  DECLARE v_aprobadas      INT;
  DECLARE v_promedio       DECIMAL(4,2);
  DECLARE v_ya_titulado    INT;
  DECLARE v_id_carrera     INT;

  proc: BEGIN

    -- 1. Contar total de materias del plan
    SELECT COUNT(*) INTO v_total_materias
    FROM Materia
    WHERE id_plan = p_id_plan;

    -- 2. Contar materias aprobadas por el alumno en ese plan
    SELECT COUNT(DISTINCT C.id_materia) INTO v_aprobadas
    FROM Inscripcion I
    JOIN Comision    C ON C.id_comision = I.id_comision
    JOIN Materia     M ON M.id_materia  = C.id_materia
    WHERE I.id_alumno = p_legajo
      AND I.estado    = 'aprobada'
      AND M.id_plan   = p_id_plan;

    -- 3. Verificar que todas las materias estén aprobadas
    IF v_aprobadas < v_total_materias THEN
      SELECT CONCAT('ERROR: Faltan ', v_total_materias - v_aprobadas, ' materia(s) por aprobar') AS mensaje;
      LEAVE proc;
    END IF;

    -- 4. Calcular promedio de notas finales aprobadas del alumno en el plan
    SELECT ROUND(AVG(N.calificacion), 2) INTO v_promedio
    FROM Nota        N
    JOIN Inscripcion I ON I.id_inscripcion = N.id_inscripcion
    JOIN Comision    C ON C.id_comision    = I.id_comision
    JOIN Materia     M ON M.id_materia     = C.id_materia
    WHERE I.id_alumno    = p_legajo
      AND I.estado       = 'aprobada'
      AND M.id_plan      = p_id_plan
      AND N.tipo         = 'final'
      AND N.calificacion >= 4;

    -- 5. Verificar que el título no haya sido otorgado previamente
    SELECT COUNT(*) INTO v_ya_titulado
    FROM Titulo_Obtenido
    WHERE id_alumno = p_legajo
      AND id_plan   = p_id_plan;

    IF v_ya_titulado > 0 THEN
      SELECT 'ERROR: El título ya fue otorgado' AS mensaje;
      LEAVE proc;
    END IF;

    -- 6. Obtener la carrera del plan
    SELECT id_carrera INTO v_id_carrera
    FROM Plan
    WHERE id_plan = p_id_plan;

    -- 7. Registrar el título obtenido
    INSERT INTO Titulo_Obtenido (id_alumno, id_carrera, id_plan, fecha_egreso, promedio_final)
    VALUES (p_legajo, v_id_carrera, p_id_plan, CURDATE(), v_promedio);

    -- 8. Confirmar operación exitosa
    SELECT CONCAT('OK: Título otorgado. Promedio final: ', IFNULL(v_promedio, 'S/D')) AS mensaje;

  END; -- fin proc
END$$

DELIMITER ;

-- =============================================================================
-- TRIGGERS
-- =============================================================================

DELIMITER $$

-- -----------------------------------------------------------------------------
-- T1 — trg_after_insert_inscripcion
-- Registra en el log cada nueva inscripción creada
-- -----------------------------------------------------------------------------
CREATE TRIGGER trg_after_insert_inscripcion
AFTER INSERT ON Inscripcion
FOR EACH ROW
BEGIN
  INSERT INTO Log_Inscripciones (id_inscripcion, legajo_alumno, id_comision, accion, fecha_evento)
  VALUES (NEW.id_inscripcion, NEW.id_alumno, NEW.id_comision, 'INSCRIPCION_CREADA', NOW());
END$$

-- -----------------------------------------------------------------------------
-- T2 — trg_before_insert_comision
-- Impide crear una comisión si el aula ya está ocupada en ese turno y ciclo
-- -----------------------------------------------------------------------------
CREATE TRIGGER trg_before_insert_comision
BEFORE INSERT ON Comision
FOR EACH ROW
BEGIN
  IF EXISTS (
    SELECT 1
    FROM Comision
    WHERE id_aula  = NEW.id_aula
      AND turno    = NEW.turno
      AND id_ciclo = NEW.id_ciclo
  ) THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'ERROR: El aula ya tiene una comisión asignada en ese turno y ciclo';
  END IF;
END$$

-- -----------------------------------------------------------------------------
-- T3 — trg_after_update_ciclo
-- Garantiza que solo un ciclo académico esté activo a la vez
-- -----------------------------------------------------------------------------
CREATE TRIGGER trg_after_update_ciclo
AFTER UPDATE ON Ciclo_Academico
FOR EACH ROW
BEGIN
  IF NEW.activo = 1 AND OLD.activo = 0 THEN
    UPDATE Ciclo_Academico
    SET activo = 0
    WHERE id_ciclo != NEW.id_ciclo;
  END IF;
END$$

-- -----------------------------------------------------------------------------
-- T4 — trg_after_insert_nota
-- Marca la inscripción como aprobada al registrar una nota final >= 4
-- -----------------------------------------------------------------------------
CREATE TRIGGER trg_after_insert_nota
AFTER INSERT ON Nota
FOR EACH ROW
BEGIN
  IF NEW.tipo = 'final' AND NEW.calificacion >= 4 THEN
    UPDATE Inscripcion
    SET estado = 'aprobada'
    WHERE id_inscripcion = NEW.id_inscripcion;
  END IF;
END$$

DELIMITER ;

-- =============================================================================
-- VISTAS
-- =============================================================================

-- -----------------------------------------------------------------------------
-- V1 — v_comisiones_activas
-- Muestra todas las comisiones del ciclo académico activo con cantidad de inscriptos
-- -----------------------------------------------------------------------------
CREATE VIEW v_comisiones_activas AS
SELECT
  C.id_comision,
  M.nombre                                  AS nombre_materia,
  CONCAT(P.apellido, ', ', P.nombre)        AS profesor,
  A.numero                                  AS aula,
  A.edificio,
  C.turno,
  C.cupo_maximo,
  CA.anio,
  CA.cuatrimestre,
  COUNT(I.id_inscripcion)                   AS inscriptos_activos
FROM Comision        C
JOIN Materia         M  ON M.id_materia  = C.id_materia
JOIN Profesor        P  ON P.id_profesor = C.id_profesor
JOIN Aula            A  ON A.id_aula     = C.id_aula
JOIN Ciclo_Academico CA ON CA.id_ciclo   = C.id_ciclo
LEFT JOIN Inscripcion I ON I.id_comision = C.id_comision
                        AND I.estado IN ('activa', 'aprobada')
WHERE CA.activo = 1
GROUP BY
  C.id_comision,
  M.nombre,
  P.apellido,
  P.nombre,
  A.numero,
  A.edificio,
  C.turno,
  C.cupo_maximo,
  CA.anio,
  CA.cuatrimestre;

-- -----------------------------------------------------------------------------
-- V2 — v_historial_alumno
-- Historial completo de cursadas y notas por alumno
-- -----------------------------------------------------------------------------
CREATE VIEW v_historial_alumno AS
SELECT
  I.id_alumno                               AS legajo,
  M.nombre                                  AS nombre_materia,
  CA.anio,
  CA.cuatrimestre,
  CONCAT(P.apellido, ', ', P.nombre)        AS profesor,
  N.tipo                                    AS tipo_nota,
  N.calificacion,
  N.fecha                                   AS fecha_nota,
  I.estado                                  AS estado_inscripcion,
  I.id_inscripcion,
  I.id_comision
FROM Inscripcion      I
JOIN Comision         C  ON C.id_comision  = I.id_comision
JOIN Materia          M  ON M.id_materia   = C.id_materia
JOIN Ciclo_Academico  CA ON CA.id_ciclo    = C.id_ciclo
JOIN Profesor         P  ON P.id_profesor  = C.id_profesor
LEFT JOIN Nota        N  ON N.id_inscripcion = I.id_inscripcion;

-- -----------------------------------------------------------------------------
-- V3 — v_promedio_por_alumno
-- Promedio de notas finales aprobadas por alumno
-- -----------------------------------------------------------------------------
CREATE VIEW v_promedio_por_alumno AS
SELECT
  I.id_alumno          AS legajo,
  ROUND(AVG(N.calificacion), 2) AS promedio_final
FROM Inscripcion I
JOIN Nota        N ON N.id_inscripcion = I.id_inscripcion
WHERE N.tipo         = 'final'
  AND N.calificacion >= 4
GROUP BY I.id_alumno;

-- -----------------------------------------------------------------------------
-- V4 — v_asistencia_por_inscripcion
-- Resumen de asistencia y porcentaje por inscripción
-- -----------------------------------------------------------------------------
CREATE VIEW v_asistencia_por_inscripcion AS
SELECT
  id_inscripcion,
  COUNT(*)                                      AS total_clases,
  SUM(presente)                                 AS clases_presentes,
  ROUND((SUM(presente) / COUNT(*)) * 100, 1)   AS porcentaje_asistencia
FROM Asistencia
GROUP BY id_inscripcion;

-- -----------------------------------------------------------------------------
-- V5 — v_estado_plan_carrera
-- Estado de cada materia del plan para cada alumno (aprobada/en curso/pendiente)
-- -----------------------------------------------------------------------------
CREATE VIEW v_estado_plan_carrera AS
SELECT
  AL.legajo,
  M.id_materia,
  M.nombre         AS nombre_materia,
  M.año_cursada,
  M.cuatrimestre,
  CASE
    WHEN I.estado = 'aprobada'    THEN 'Aprobada'
    WHEN I.estado = 'activa'      THEN 'En curso'
    WHEN I.estado = 'desaprobada' THEN 'Desaprobada'
    ELSE 'Pendiente'
  END              AS estado
FROM Alumno  AL
JOIN Plan    PL ON PL.id_carrera = AL.id_carrera
JOIN Materia M  ON M.id_plan     = PL.id_plan
LEFT JOIN (
  SELECT I2.id_alumno, C2.id_materia, I2.estado
  FROM Inscripcion I2
  JOIN Comision    C2 ON C2.id_comision = I2.id_comision
  WHERE I2.estado != 'baja'
) I ON I.id_alumno = AL.legajo AND I.id_materia = M.id_materia;

-- =============================================================================
-- DATOS INICIALES
-- =============================================================================

-- Roles del sistema
INSERT INTO Rol (nombre) VALUES
  ('Administrador'),
  ('Bedel'),
  ('Docente'),
  ('Alumno');

-- Usuario administrador (contraseña: admin123 — hash bcrypt)
INSERT INTO Usuario (email, password_hash, id_rol, legajo_alumno, id_profesor, activo)
VALUES (
  'admin@academi.edu',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  1,
  NULL,
  NULL,
  1
);

-- Usuario bedel (contraseña: admin123 — hash bcrypt)
INSERT INTO Usuario (email, password_hash, id_rol, legajo_alumno, id_profesor, activo)
VALUES (
  'bedel@academi.edu',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  2,
  NULL,
  NULL,
  1
);

-- =============================================================================
-- VERIFICACIÓN FINAL (ejecutar manualmente para confirmar la instalación)
-- =============================================================================

-- SELECT * FROM Rol;
-- SELECT * FROM Usuario;
-- SHOW TABLES;
-- SELECT ROUTINE_NAME, ROUTINE_TYPE FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = 'academisys';
-- SELECT TRIGGER_NAME FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = 'academisys';
-- SELECT TABLE_NAME FROM information_schema.VIEWS WHERE TABLE_SCHEMA = 'academisys';
