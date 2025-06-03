-- 1. Marcas :check:
CREATE TABLE brands (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    logo VARCHAR(255),
    description TEXT
);

-- 2. Colores :check:
CREATE TABLE colors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

-- 3. Tipos de vehículos :check:
CREATE TABLE vehiclestypes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

-- 4. Modelos de marca :check:
CREATE TABLE brandmodels (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(100) NOT NULL,
    description TEXT,
    brand_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (brand_id) REFERENCES brands(id)
);

-- 5. Tipos de empleados :check:
CREATE TABLE EmployeeType (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);

-- 6. Empleados :check:
CREATE TABLE Employees (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dni VARCHAR(10) UNIQUE,
    lastnames VARCHAR(200),
    names VARCHAR(100),
    birthday DATE,
    license VARCHAR(20),
    address VARCHAR(200),
    email VARCHAR(100),
    photo VARCHAR(100),
    phone VARCHAR(20),
    status BOOLEAN DEFAULT TRUE,
    password VARCHAR(255),
    type_id BIGINT UNSIGNED,
    FOREIGN KEY (type_id) REFERENCES EmployeeType(id)
);

-- 7. Motivos/razones :check:
CREATE TABLE Reasons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    description TEXT
);

-- 8. Vehículos :check:
CREATE TABLE vehicles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(100) NOT NULL,
    plate VARCHAR(20) NOT NULL,
    year INT NOT NULL,
    load_capacity DOUBLE NOT NULL,
    description TEXT,
    status BOOLEAN DEFAULT TRUE,
    color_id BIGINT UNSIGNED NOT NULL,
    brand_id BIGINT UNSIGNED NOT NULL,
    type_id BIGINT UNSIGNED NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (color_id) REFERENCES colors(id),
    FOREIGN KEY (brand_id) REFERENCES brands(id),
    FOREIGN KEY (type_id) REFERENCES vehiclestypes(id),
    FOREIGN KEY (model_id) REFERENCES brandmodels(id)
);

-- 9. Turnos :check:
CREATE TABLE Shifts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    description TEXT
);

-- 10. Zonas :check:
CREATE TABLE Zones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    description TEXT
);

-- 11. Coordenadas :check:
CREATE TABLE Coords (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    coord_index INT,
    type_coord INT, -- Zona o ruta
    longitude DECIMAL(10,7),
    latitude DECIMAL(10,7),
    zone_id BIGINT UNSIGNED,
    FOREIGN KEY (zone_id) REFERENCES Zones(id)
);

-- 12. Grupos de empleados :check:
CREATE TABLE EmployeeGroups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    zone_id BIGINT UNSIGNED,
    shift_id BIGINT UNSIGNED,
    vehicle_id BIGINT UNSIGNED,
    days VARCHAR(50),
    status INT, -- Activo (1), Inactivo (0)
    FOREIGN KEY (zone_id) REFERENCES Zones(id),
    FOREIGN KEY (shift_id) REFERENCES Shifts(id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);

-- 13. Detalle de grupos de empleados :check:
CREATE TABLE GroupDetails (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    group_id BIGINT UNSIGNED,
    employee_id BIGINT UNSIGNED,
    FOREIGN KEY (group_id) REFERENCES EmployeeGroups(id),
    FOREIGN KEY (employee_id) REFERENCES Employees(id)
);

CREATE TABLE ConfigGroups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    group_id BIGINT UNSIGNED,
    employee_id BIGINT UNSIGNED,
    FOREIGN KEY (group_id) REFERENCES EmployeeGroups(id),
    FOREIGN KEY (employee_id) REFERENCES Employees(id)
);

-- 14. Programación / Scheduling :check:
CREATE TABLE Scheduling (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    group_id BIGINT UNSIGNED,
    date DATE,
    status INT, -- Completado (1), Pendiente (0),Reprogramado (2), Cancelado (3)
    notes TEXT,
    FOREIGN KEY (group_id) REFERENCES EmployeeGroups(id)
);

-- 15. Cambios :check:
CREATE TABLE Changes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    new_employee_id BIGINT UNSIGNED,
    old_employee_id BIGINT UNSIGNED,
    new_vehicle_id BIGINT UNSIGNED,
    old_vehicle_id BIGINT UNSIGNED,
    shift_id BIGINT UNSIGNED,
    reason_id BIGINT UNSIGNED,
    change_date DATE,
    FOREIGN KEY (new_employee_id) REFERENCES Employees(id),
    FOREIGN KEY (old_employee_id) REFERENCES Employees(id),
    FOREIGN KEY (new_vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (old_vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (shift_id) REFERENCES Shifts(id),
    FOREIGN KEY (reason_id) REFERENCES Reasons(id)
);

-- Contratos
CREATE TABLE Contracts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED NOT NULL,
    contract_type INT NOT NULL, 
    start_date DATE NOT NULL,
    end_date DATE, 
    salary DECIMAL(10,2) NOT NULL,
    position_id BIGINT UNSIGNED NOT NULL, 
    department_id BIGINT UNSIGNED NOT NULL,
    vacation_days_per_year INT NOT NULL, 
    probation_period_months INT DEFAULT 3,
    is_active BOOLEAN DEFAULT TRUE,
    termination_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES Employees(id),
    FOREIGN KEY (position_id) REFERENCES EmployeeType(id),
    FOREIGN KEY (department_id) REFERENCES Departments(id)
);

-- 16. Vacaciones :check:
CREATE TABLE Vacations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED,
    request_date DATE,
    end_date DATE,
    requested_days INT,
    available_days INT,
    status VARCHAR(50),
    notes TEXT,
    FOREIGN KEY (employee_id) REFERENCES Employees(id)
);

-- 17. Asistencias :check:
CREATE TABLE Attendances (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED NOT NULL,
    attendance_date DATE NOT NULL,
    status INT DEFAULT 0 , -- 0: Pendiente, 1: Asistió, 2: Falta
    notes TEXT NULL,
    FOREIGN KEY (employee_id) REFERENCES Employees(id),
);
