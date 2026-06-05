"""
seed_academisys.py
Crea la base de datos, todas las tablas y popula con datos de prueba realistas.

Cambios respecto a la versión anterior:
  - Tabla Ciclo_Academico agregada (con campo activo y FK en Comision)
  - Comision ya no tiene año/cuatrimestre sueltos, usa id_ciclo
  - Tabla Log_Inscripciones agregada (destino del trigger de auditoría)
  - Usuario.legajo_docente renombrado a id_profesor (más claro)
  - Comentarios unificados usando "Profesor" consistentemente

Requisitos:
    pip install mysql-connector-python faker

Uso:
    python seed_academisys.py
"""

import random
from datetime import date, timedelta
import mysql.connector
from faker import Faker

# ── Configuración ────────────────────────────────────────────
DB_CONFIG = {
    "host": "localhost",
    "port": 3306,
    "user": "root",
    "password": "",  # cambiá si tenés contraseña
}

fake = Faker("es_AR")
random.seed(42)

# ── Cantidad de registros ────────────────────────────────────
N_PROFESORES = 200
N_ALUMNOS = 1000
N_COMISIONES = 500
N_INSCRIPCIONES = 5000
N_TITULOS = 80
N_PLANES_CON_MATERIAS = 15


# ── Helpers ──────────────────────────────────────────────────
def random_date(start: date, end: date) -> date:
    return start + timedelta(days=random.randint(0, (end - start).days))


def run(cursor, sql, params=None):
    cursor.execute(sql, params or ())


# ── Datos base ───────────────────────────────────────────────
CARRERAS = [
    ("Ingeniería en Sistemas", 5, "Ingeniero en Sistemas", "presencial"),
    ("Contador Público", 4, "Contador Público", "semipresencial"),
    ("Medicina", 6, "Médico", "presencial"),
    ("Derecho", 5, "Abogado", "presencial"),
    ("Arquitectura", 5, "Arquitecto", "presencial"),
    ("Ingeniería Civil", 5, "Ingeniero Civil", "presencial"),
    ("Psicología", 5, "Psicólogo", "semipresencial"),
    ("Administración de Empresas", 4, "Licenciado en Administración", "virtual"),
    ("Diseño Gráfico", 4, "Diseñador Gráfico", "presencial"),
    ("Enfermería", 3, "Licenciado en Enfermería", "presencial"),
]

MATERIAS_BASE = [
    ("Álgebra", "MAT01", 1, 1, 80),
    ("Análisis Matemático I", "MAT02", 1, 2, 80),
    ("Análisis Matemático II", "ANA02", 2, 1, 80),
    ("Análisis Matemático III", "ANA03", 2, 2, 80),
    ("Álgebra Lineal", "ALG01", 2, 1, 60),
    ("Programación I", "PRG01", 1, 1, 60),
    ("Programación II", "PRG02", 1, 2, 60),
    ("Programación III", "PRG03", 2, 1, 60),
    ("Base de Datos I", "BD01", 2, 2, 90),
    ("Base de Datos II", "BD02", 3, 1, 90),
    ("Redes I", "RED01", 3, 1, 60),
    ("Redes II", "RED02", 3, 2, 60),
    ("Sistemas Operativos I", "SO01", 3, 1, 60),
    ("Sistemas Operativos II", "SO02", 3, 2, 60),
    ("Inglés Técnico I", "ING01", 1, 1, 40),
    ("Inglés Técnico II", "ING02", 2, 1, 40),
    ("Física I", "FIS01", 1, 2, 80),
    ("Física II", "FIS02", 2, 1, 80),
    ("Química", "QUI01", 1, 1, 60),
    ("Economía", "ECO01", 2, 2, 60),
    ("Laboratorio I", "LAB01", 2, 1, 40),
    ("Laboratorio II", "LAB02", 3, 1, 40),
    ("Diseño de Sistemas I", "DIS01", 3, 2, 60),
    ("Diseño de Sistemas II", "DIS02", 4, 1, 60),
    ("Arquitectura de Software", "ARQ01", 4, 1, 60),
    ("Sistemas de Información I", "SIS01", 3, 1, 60),
    ("Sistemas de Información II", "SIS02", 4, 2, 60),
    ("Comunicaciones", "COM01", 4, 1, 60),
    ("Estadística", "EST01", 2, 2, 60),
    ("Legislación Informática", "LEG01", 5, 1, 40),
]

CORRELATIVAS = [
    (1, 0),
    (2, 1),
    (3, 2),
    (4, 0),
    (6, 5),
    (7, 6),
    (8, 6),
    (9, 8),
    (11, 10),
    (13, 12),
    (17, 16),
    (21, 20),
    (23, 22),
    (26, 25),
]

AULAS = [
    ("A101", "Edificio A", 40, "Proyector"),
    ("A102", "Edificio A", 35, "Proyector y Aire acondicionado"),
    ("A103", "Edificio A", 50, "Completo"),
    ("B201", "Edificio B", 45, "Proyector"),
    ("B202", "Edificio B", 30, "Aire acondicionado"),
    ("B203", "Edificio B", 60, "Completo"),
    ("C301", "Edificio C", 80, "Proyector"),
    ("C302", "Edificio C", 70, "Pizarrón digital"),
    ("LAB1", "Laboratorios", 25, "Laboratorio"),
    ("LAB2", "Laboratorios", 20, "Laboratorio"),
]

# Ciclos académicos: (anio, cuatrimestre, fecha_inicio, fecha_fin, activo)
# Solo uno puede tener activo=1
CICLOS = [
    (2021, 1, date(2021, 3, 1),  date(2021, 7, 31),  0),
    (2021, 2, date(2021, 8, 1),  date(2021, 12, 15), 0),
    (2022, 1, date(2022, 3, 1),  date(2022, 7, 31),  0),
    (2022, 2, date(2022, 8, 1),  date(2022, 12, 15), 0),
    (2023, 1, date(2023, 3, 1),  date(2023, 7, 31),  0),
    (2023, 2, date(2023, 8, 1),  date(2023, 12, 15), 0),
    (2024, 1, date(2024, 3, 1),  date(2024, 7, 31),  0),
    (2024, 2, date(2024, 8, 1),  date(2024, 12, 15), 0),
    (2025, 1, date(2025, 3, 1),  date(2025, 7, 31),  0),
    (2025, 2, date(2025, 8, 1),  date(2025, 12, 15), 1),  # activo
]

TIPOS_NOTA = ["parcial_1", "parcial_2", "recuperatorio", "final", "coloquio"]
ESTADOS_INS = ["activa", "baja", "aprobada", "desaprobada"]
TURNOS = ["mañana", "tarde", "noche"]


# ── SQL de creación de tablas ─────────────────────────────────
CREAR_TABLAS = [
    """CREATE TABLE IF NOT EXISTS Carrera (
        id_carrera      INT AUTO_INCREMENT PRIMARY KEY,
        nombre          VARCHAR(100) NOT NULL,
        duracion_años   INT NOT NULL,
        titulo_otorga   VARCHAR(100),
        modalidad       ENUM('presencial','semipresencial','virtual') DEFAULT 'presencial',
        activa          TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",

    """CREATE TABLE IF NOT EXISTS Plan (
        id_plan         INT AUTO_INCREMENT PRIMARY KEY,
        id_carrera      INT NOT NULL,
        año_vigencia    INT NOT NULL,
        descripcion     VARCHAR(100),
        FOREIGN KEY (id_carrera) REFERENCES Carrera(id_carrera)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",

    """CREATE TABLE IF NOT EXISTS Materia (
        id_materia      INT AUTO_INCREMENT PRIMARY KEY,
        id_plan         INT NOT NULL,
        nombre          VARCHAR(100) NOT NULL,
        codigo          VARCHAR(20)  NOT NULL UNIQUE,
        año_cursada     INT NOT NULL,
        cuatrimestre    INT NOT NULL,
        carga_horaria   INT NOT NULL,
        FOREIGN KEY (id_plan) REFERENCES Plan(id_plan)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",

    """CREATE TABLE IF NOT EXISTS Correlativa (
        id_materia      INT NOT NULL,
        id_correlativa  INT NOT NULL,
        PRIMARY KEY (id_materia, id_correlativa),
        FOREIGN KEY (id_materia)     REFERENCES Materia(id_materia),
        FOREIGN KEY (id_correlativa) REFERENCES Materia(id_materia)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",

    """CREATE TABLE IF NOT EXISTS Aula (
        id_aula         INT AUTO_INCREMENT PRIMARY KEY,
        numero          VARCHAR(10)  NOT NULL,
        edificio        VARCHAR(50),
        capacidad       INT,
        equipamiento    VARCHAR(100)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",

    """CREATE TABLE IF NOT EXISTS Profesor (
        id_profesor     INT AUTO_INCREMENT PRIMARY KEY,
        dni             VARCHAR(15)  NOT NULL UNIQUE,
        apellido        VARCHAR(60)  NOT NULL,
        nombre          VARCHAR(60)  NOT NULL,
        email           VARCHAR(150) NOT NULL UNIQUE,
        telefono        VARCHAR(30),
        fecha_ingreso   DATE,
        activo          TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",

    """CREATE TABLE IF NOT EXISTS Alumno (
        legajo           VARCHAR(20)  NOT NULL PRIMARY KEY,
        dni              VARCHAR(15)  NOT NULL UNIQUE,
        apellido         VARCHAR(60)  NOT NULL,
        nombre           VARCHAR(60)  NOT NULL,
        email            VARCHAR(150) NOT NULL UNIQUE,
        telefono         VARCHAR(30),
        fecha_nacimiento DATE,
        fecha_ingreso    DATE,
        id_carrera       INT NOT NULL,
        activo           TINYINT(1) DEFAULT 1,
        FOREIGN KEY (id_carrera) REFERENCES Carrera(id_carrera)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",

    # Nueva tabla: Ciclo_Academico
    # Cada fila representa un cuatrimestre. Solo uno puede tener activo=1.
    # El trigger trg_after_update_ciclo garantiza esa unicidad.
    """CREATE TABLE IF NOT EXISTS Ciclo_Academico (
        id_ciclo        INT AUTO_INCREMENT PRIMARY KEY,
        anio            INT NOT NULL,
        cuatrimestre    INT NOT NULL CHECK (cuatrimestre IN (1, 2)),
        fecha_inicio    DATE,
        fecha_fin       DATE,
        activo          TINYINT(1) DEFAULT 0,
        UNIQUE KEY uq_ciclo (anio, cuatrimestre)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",

    # Comision ahora referencia id_ciclo en lugar de tener año/cuatrimestre sueltos
    """CREATE TABLE IF NOT EXISTS Comision (
        id_comision     INT AUTO_INCREMENT PRIMARY KEY,
        id_materia      INT NOT NULL,
        id_profesor     INT NOT NULL,
        id_aula         INT NOT NULL,
        id_ciclo        INT NOT NULL,
        turno           ENUM('mañana','tarde','noche'),
        cupo_maximo     INT,
        FOREIGN KEY (id_materia)  REFERENCES Materia(id_materia),
        FOREIGN KEY (id_profesor) REFERENCES Profesor(id_profesor),
        FOREIGN KEY (id_aula)     REFERENCES Aula(id_aula),
        FOREIGN KEY (id_ciclo)    REFERENCES Ciclo_Academico(id_ciclo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",

    """CREATE TABLE IF NOT EXISTS Inscripcion (
        id_inscripcion    INT AUTO_INCREMENT PRIMARY KEY,
        id_alumno         VARCHAR(20) NOT NULL,
        id_comision       INT NOT NULL,
        fecha_inscripcion DATE,
        estado            ENUM('activa','baja','aprobada','desaprobada'),
        FOREIGN KEY (id_alumno)   REFERENCES Alumno(legajo),
        FOREIGN KEY (id_comision) REFERENCES Comision(id_comision)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",

    """CREATE TABLE IF NOT EXISTS Nota (
        id_nota         INT AUTO_INCREMENT PRIMARY KEY,
        id_inscripcion  INT NOT NULL,
        tipo            ENUM('parcial_1','parcial_2','recuperatorio','final','coloquio'),
        fecha           DATE,
        calificacion    DECIMAL(4,2),
        observacion     VARCHAR(100),
        FOREIGN KEY (id_inscripcion) REFERENCES Inscripcion(id_inscripcion)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",

    """CREATE TABLE IF NOT EXISTS Asistencia (
        id_asistencia   INT AUTO_INCREMENT PRIMARY KEY,
        id_inscripcion  INT NOT NULL,
        fecha_clase     DATE NOT NULL,
        presente        TINYINT(1) DEFAULT 0,
        justificada     TINYINT(1) DEFAULT 0,
        UNIQUE KEY uq_asist (id_inscripcion, fecha_clase),
        FOREIGN KEY (id_inscripcion) REFERENCES Inscripcion(id_inscripcion)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",

    """CREATE TABLE IF NOT EXISTS Titulo_Obtenido (
        id_titulo       INT AUTO_INCREMENT PRIMARY KEY,
        id_alumno       VARCHAR(20) NOT NULL,
        id_carrera      INT NOT NULL,
        id_plan         INT NOT NULL,
        fecha_egreso    DATE,
        promedio_final  DECIMAL(4,2),
        FOREIGN KEY (id_alumno)  REFERENCES Alumno(legajo),
        FOREIGN KEY (id_carrera) REFERENCES Carrera(id_carrera),
        FOREIGN KEY (id_plan)    REFERENCES Plan(id_plan)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",

    # Tabla de auditoría — destino del trigger trg_after_insert_inscripcion
    """CREATE TABLE IF NOT EXISTS Log_Inscripciones (
        id_log          INT AUTO_INCREMENT PRIMARY KEY,
        id_inscripcion  INT NOT NULL,
        legajo_alumno   VARCHAR(20),
        id_comision     INT,
        accion          VARCHAR(50),
        fecha_evento    DATETIME DEFAULT CURRENT_TIMESTAMP,
        id_usuario      INT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",

    """CREATE TABLE IF NOT EXISTS Rol (
        id_rol  INT AUTO_INCREMENT PRIMARY KEY,
        nombre  VARCHAR(60) NOT NULL UNIQUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",

    # legajo_docente renombrado a id_profesor para mayor claridad
    """CREATE TABLE IF NOT EXISTS Usuario (
        id_usuario      INT AUTO_INCREMENT PRIMARY KEY,
        email           VARCHAR(150) NOT NULL UNIQUE,
        password_hash   VARCHAR(255) NOT NULL,
        id_rol          INT NOT NULL,
        legajo_alumno   VARCHAR(20) NULL,
        id_profesor     INT NULL,
        activo          TINYINT(1) DEFAULT 1,
        created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_rol)        REFERENCES Rol(id_rol),
        FOREIGN KEY (legajo_alumno) REFERENCES Alumno(legajo),
        FOREIGN KEY (id_profesor)   REFERENCES Profesor(id_profesor)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",
]


# ────────────────────────────────────────────────────────────
def seed():
    conn = mysql.connector.connect(**DB_CONFIG)
    cur = conn.cursor()

    print("Creando base de datos si no existe...")
    cur.execute(
        "CREATE DATABASE IF NOT EXISTS academisys CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    )
    cur.execute("USE academisys")
    conn.database = "academisys"

    print("Creando tablas...")
    for stmt in CREAR_TABLAS:
        cur.execute(stmt)
    conn.commit()

    print("Limpiando tablas...")
    tablas = [
        "Usuario",
        "Rol",
        "Log_Inscripciones",
        "Titulo_Obtenido",
        "Asistencia",
        "Nota",
        "Inscripcion",
        "Comision",
        "Ciclo_Academico",
        "Correlativa",
        "Materia",
        "Plan",
        "Alumno",
        "Profesor",
        "Aula",
        "Carrera",
    ]
    cur.execute("SET FOREIGN_KEY_CHECKS = 0")
    for t in tablas:
        cur.execute(f"TRUNCATE TABLE {t}")
    cur.execute("SET FOREIGN_KEY_CHECKS = 1")
    conn.commit()

    # ── 1. Carrera ───────────────────────────────────────────
    print("Insertando Carreras...")
    for c in CARRERAS:
        run(
            cur,
            "INSERT INTO Carrera (nombre, duracion_años, titulo_otorga, modalidad, activa) VALUES (%s, %s, %s, %s, TRUE)",
            c,
        )
    conn.commit()
    id_carreras = list(range(1, len(CARRERAS) + 1))

    # ── 2. Plan ──────────────────────────────────────────────
    print("Insertando Planes...")
    plan_ids = []
    for id_c in id_carreras:
        for año in range(2018, 2025):
            run(
                cur,
                "INSERT INTO Plan (id_carrera, año_vigencia, descripcion) VALUES (%s, %s, %s)",
                (id_c, año, random.choice(["Plan Vigente", "Plan Reformado", "Plan Transitorio"])),
            )
            plan_ids.append(cur.lastrowid)
    conn.commit()

    # ── 3. Materia ───────────────────────────────────────────
    print("Insertando Materias...")
    materia_ids = []
    for plan_id in plan_ids[:N_PLANES_CON_MATERIAS]:
        for mat in MATERIAS_BASE:
            nombre, codigo_base, año_c, cuatri, carga = mat
            codigo = f"{codigo_base}_{plan_id}"
            run(
                cur,
                "INSERT INTO Materia (id_plan, nombre, codigo, año_cursada, cuatrimestre, carga_horaria) VALUES (%s, %s, %s, %s, %s, %s)",
                (plan_id, nombre, codigo, año_c, cuatri, carga),
            )
            materia_ids.append(cur.lastrowid)
    conn.commit()

    primer_bloque = materia_ids[: len(MATERIAS_BASE)]

    # ── 4. Correlativa ───────────────────────────────────────
    print("Insertando Correlativas...")
    seen_corr = set()
    for mi, ci in CORRELATIVAS:
        if mi < len(primer_bloque) and ci < len(primer_bloque):
            pair = (primer_bloque[mi], primer_bloque[ci])
            if pair not in seen_corr and pair[0] != pair[1]:
                run(cur, "INSERT INTO Correlativa (id_materia, id_correlativa) VALUES (%s, %s)", pair)
                seen_corr.add(pair)
    conn.commit()

    # ── 5. Aula ──────────────────────────────────────────────
    print("Insertando Aulas...")
    aula_ids = []
    for a in AULAS:
        run(cur, "INSERT INTO Aula (numero, edificio, capacidad, equipamiento) VALUES (%s, %s, %s, %s)", a)
        aula_ids.append(cur.lastrowid)
    conn.commit()

    # ── 6. Ciclo_Academico ───────────────────────────────────
    print("Insertando Ciclos Académicos...")
    ciclo_ids = []
    for ciclo in CICLOS:
        anio, cuatrimestre, fecha_inicio, fecha_fin, activo = ciclo
        run(
            cur,
            "INSERT INTO Ciclo_Academico (anio, cuatrimestre, fecha_inicio, fecha_fin, activo) VALUES (%s, %s, %s, %s, %s)",
            (anio, cuatrimestre, fecha_inicio, fecha_fin, activo),
        )
        ciclo_ids.append(cur.lastrowid)
    conn.commit()
    print(f"  → {len(ciclo_ids)} ciclos insertados (activo: 2025 C2)")

    # ── 7. Profesor ──────────────────────────────────────────
    print(f"Insertando Profesores ({N_PROFESORES})...")
    profesor_ids = []
    profesor_data = []
    used_dni_p = set()
    used_email_p = set()
    for i in range(N_PROFESORES):
        while True:
            dni = str(random.randint(10_000_000, 40_000_000))
            if dni not in used_dni_p:
                used_dni_p.add(dni)
                break
        while True:
            email = fake.unique.email()
            if email not in used_email_p:
                used_email_p.add(email)
                break
        run(
            cur,
            "INSERT INTO Profesor (dni, apellido, nombre, email, telefono, fecha_ingreso, activo) VALUES (%s, %s, %s, %s, %s, %s, %s)",
            (
                dni,
                fake.last_name(),
                fake.first_name(),
                email,
                fake.phone_number()[:30],
                random_date(date(2000, 1, 1), date(2023, 12, 31)),
                random.choice([True, True, True, False]),
            ),
        )
        prof_id = cur.lastrowid
        profesor_ids.append(prof_id)
        profesor_data.append((prof_id, email))
    conn.commit()

    # ── 8. Alumno ────────────────────────────────────────────
    print(f"Insertando Alumnos ({N_ALUMNOS})...")
    alumno_ids = []
    alumno_data = []
    used_dni_a = set()
    used_email_a = set()
    for i in range(1, N_ALUMNOS + 1):
        while True:
            dni = str(random.randint(40_000_001, 99_000_000))
            if dni not in used_dni_a:
                used_dni_a.add(dni)
                break
        while True:
            email = fake.unique.email()
            if email not in used_email_a:
                used_email_a.add(email)
                break
        legajo = f"A-{i:04d}"
        run(
            cur,
            "INSERT INTO Alumno (legajo, dni, apellido, nombre, email, telefono, fecha_nacimiento, fecha_ingreso, id_carrera, activo) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            (
                legajo,
                dni,
                fake.last_name(),
                fake.first_name(),
                email,
                fake.phone_number()[:30],
                random_date(date(1990, 1, 1), date(2005, 12, 31)),
                random_date(date(2018, 1, 1), date(2024, 3, 1)),
                random.choice(id_carreras),
                random.choice([True, True, True, False]),
            ),
        )
        al_id = cur.lastrowid
        alumno_ids.append(al_id)
        alumno_data.append((legajo, email))
    conn.commit()

    legajos = [legajo for legajo, _ in alumno_data]

    # ── 9. Comision ──────────────────────────────────────────
    # Ahora usa id_ciclo en lugar de año/cuatrimestre sueltos.
    # El trigger trg_before_insert_comision bloquea duplicados de aula+turno+ciclo,
    # por eso envolvemos cada insert en try/except y simplemente salteamos los conflictos.
    print(f"Insertando Comisiones (objetivo: {N_COMISIONES})...")
    comision_ids = []
    mat_sample = materia_ids[: N_PLANES_CON_MATERIAS * len(MATERIAS_BASE)]
    intentos_comision = 0
    while len(comision_ids) < N_COMISIONES and intentos_comision < N_COMISIONES * 5:
        intentos_comision += 1
        try:
            run(
                cur,
                "INSERT INTO Comision (id_materia, id_profesor, id_aula, id_ciclo, turno, cupo_maximo) VALUES (%s, %s, %s, %s, %s, %s)",
                (
                    random.choice(mat_sample),
                    random.choice(profesor_ids),
                    random.choice(aula_ids),
                    random.choice(ciclo_ids),
                    random.choice(TURNOS),
                    random.randint(20, 40),
                ),
            )
            comision_ids.append(cur.lastrowid)
            conn.commit()
        except Exception:
            # Conflicto de aula+turno+ciclo detectado por el trigger — se saltea
            conn.rollback()
    print(f"  → {len(comision_ids)} comisiones insertadas ({intentos_comision} intentos)")

    # ── 10. Inscripcion ──────────────────────────────────────
    print(f"Insertando Inscripciones ({N_INSCRIPCIONES})...")
    inscripcion_ids = []
    seen_ins = set()
    intentos = 0
    while len(inscripcion_ids) < N_INSCRIPCIONES and intentos < N_INSCRIPCIONES * 10:
        intentos += 1
        legajo = random.choice(legajos)
        co = random.choice(comision_ids)
        if (legajo, co) in seen_ins:
            continue
        seen_ins.add((legajo, co))
        run(
            cur,
            "INSERT INTO Inscripcion (id_alumno, id_comision, fecha_inscripcion, estado) VALUES (%s, %s, %s, %s)",
            (
                legajo,
                co,
                random_date(date(2021, 1, 1), date(2024, 12, 31)),
                random.choice(ESTADOS_INS),
            ),
        )
        inscripcion_ids.append(cur.lastrowid)
    conn.commit()
    print(f"  → {len(inscripcion_ids)} inscripciones insertadas")

    # ── 11. Nota ─────────────────────────────────────────────
    print("Insertando Notas...")
    for ins_id in inscripcion_ids:
        n_notas = random.randint(1, 3)
        tipos_usados = random.sample(TIPOS_NOTA, n_notas)
        for tipo in tipos_usados:
            run(
                cur,
                "INSERT INTO Nota (id_inscripcion, tipo, fecha, calificacion, observacion) VALUES (%s, %s, %s, %s, %s)",
                (
                    ins_id,
                    tipo,
                    random_date(date(2021, 3, 1), date(2024, 12, 31)),
                    round(random.uniform(0, 10), 2),
                    random.choice(["Aprobado", "Desaprobado", "Ausente", "Muy bien", "Regular", "Excelente", None]),
                ),
            )
    conn.commit()

    # ── 12. Asistencia ───────────────────────────────────────
    print("Insertando Asistencias...")
    seen_asist = set()
    count_asist = 0
    for ins_id in inscripcion_ids:
        n_clases = random.randint(3, 8)
        fechas = set()
        while len(fechas) < n_clases:
            fechas.add(random_date(date(2021, 3, 1), date(2024, 12, 15)))
        for f in fechas:
            key = (ins_id, f)
            if key in seen_asist:
                continue
            seen_asist.add(key)
            presente = random.random() > 0.2
            run(
                cur,
                "INSERT INTO Asistencia (id_inscripcion, fecha_clase, presente, justificada) VALUES (%s, %s, %s, %s)",
                (ins_id, f, presente, (not presente) and (random.random() > 0.5)),
            )
            count_asist += 1
    conn.commit()
    print(f"  → {count_asist} asistencias insertadas")

    # ── 13. Titulo_Obtenido ──────────────────────────────────
    print(f"Insertando Títulos Obtenidos ({N_TITULOS})...")
    alumnos_titulados = random.sample(legajos, N_TITULOS)
    for legajo in alumnos_titulados:
        id_c = random.choice(id_carreras)
        id_p = random.choice(plan_ids[:20])
        run(
            cur,
            "INSERT INTO Titulo_Obtenido (id_alumno, id_carrera, id_plan, fecha_egreso, promedio_final) VALUES (%s, %s, %s, %s, %s)",
            (legajo, id_c, id_p, random_date(date(2022, 1, 1), date(2024, 12, 31)), round(random.uniform(6.0, 10.0), 2)),
        )
    conn.commit()

    # ── 14. Rol ──────────────────────────────────────────────
    print("Insertando Roles...")
    for nombre_rol in ["Administrador", "Bedel", "Docente", "Alumno"]:
        run(cur, "INSERT INTO Rol (nombre) VALUES (%s)", (nombre_rol,))
    conn.commit()

    # ── 15. Usuario ──────────────────────────────────────────
    # legajo_docente → id_profesor
    print("Insertando Usuarios...")
    HASH_PASSWORD = "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi"
    used_emails_u = set()

    run(
        cur,
        "INSERT INTO Usuario (email, password_hash, id_rol, legajo_alumno, id_profesor, activo) VALUES (%s, %s, 1, NULL, NULL, 1)",
        ("admin@academi.edu", HASH_PASSWORD),
    )
    used_emails_u.add("admin@academi.edu")

    run(
        cur,
        "INSERT INTO Usuario (email, password_hash, id_rol, legajo_alumno, id_profesor, activo) VALUES (%s, %s, 2, NULL, NULL, 1)",
        ("bedel@academi.edu", HASH_PASSWORD),
    )
    used_emails_u.add("bedel@academi.edu")

    for prof_id, prof_email in profesor_data:
        email = prof_email if prof_email not in used_emails_u else f"prof{prof_id}@academi.edu"
        used_emails_u.add(email)
        run(
            cur,
            "INSERT INTO Usuario (email, password_hash, id_rol, legajo_alumno, id_profesor, activo) VALUES (%s, %s, 3, NULL, %s, 1)",
            (email, HASH_PASSWORD, prof_id),
        )

    for legajo, al_email in alumno_data:
        email = al_email if al_email not in used_emails_u else f"{legajo.replace('-','').lower()}@academi.edu"
        used_emails_u.add(email)
        run(
            cur,
            "INSERT INTO Usuario (email, password_hash, id_rol, legajo_alumno, id_profesor, activo) VALUES (%s, %s, 4, %s, NULL, 1)",
            (email, HASH_PASSWORD, legajo),
        )

    conn.commit()
    total_usuarios = 2 + len(profesor_data) + len(alumno_data)
    print(f"  → {total_usuarios} usuarios insertados")

    cur.close()
    conn.close()

    print("\n✅ Seed completado exitosamente.")
    print("\nResumen:")
    print(f"  Ciclos académicos: {len(ciclo_ids)} (activo: 2025 C2)")
    print(f"  Profesores:        {N_PROFESORES}")
    print(f"  Alumnos:           {N_ALUMNOS}")
    print(f"  Comisiones:        {N_COMISIONES}")
    print(f"  Inscripciones:     {len(inscripcion_ids)}")
    print(f"  Títulos:           {N_TITULOS}")
    print(f"  Usuarios:          {total_usuarios}")
    print("\nCredenciales de prueba (password: password)")
    print("  admin@academi.edu  → Administrador")
    print("  bedel@academi.edu  → Bedel")
    print("  email del profesor → Docente")
    print("  email del alumno   → Alumno")


if __name__ == "__main__":
    seed()