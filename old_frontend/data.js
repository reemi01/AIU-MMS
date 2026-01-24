// Hardcoded sample data for AIU-MMS frontend demo
const SAMPLE_USERS = [
  { id: 'worker1', role: 'worker', username: 'worker1', password: 'workerpass', name: 'Ali Ahmad', avatar: 'A', trades: ['lift','chiller'] },
  { id: 'worker2', role: 'worker', username: 'worker2', password: 'workerpass2', name: 'Sara Khan', avatar: 'S', trades: ['lift'] },
  { id: 'admin', role: 'admin', username: 'admin', password: 'adminpass', name: 'Admin User', avatar: 'M' }
];

const SAMPLE_WORKERS = [
  { id: 'worker1', name: 'Ali Ahmad', phone: '0321-555-0101', trade: 'Lift & Chiller', tasksAssigned: 5 },
  { id: 'worker2', name: 'Sara Khan', phone: '0321-555-0102', trade: 'Lift', tasksAssigned: 3 }
];

const SAMPLE_EQUIPMENT = [
  { id: 'lift-1', type: 'Lift', location: 'Building A - Block 1', status: 'operational', lastMaint: '2025-09-10' },
  { id: 'chiller-1', type: 'Chiller', location: 'Roof - Building C', status: 'warning', lastMaint: '2025-09-25' }
];

const SAMPLE_TASKS = [
  { id: 'T-001', title: 'Monthly lift inspection', due: '2025-10-21', equipmentId: 'lift-1', assignedTo: 'worker1', status: 'pending', priority: 'high', notes: 'Check brakes and cables' },
  { id: 'T-002', title: 'Chiller coolant level check', due: '2025-10-20', equipmentId: 'chiller-1', assignedTo: 'worker1', status: 'in-progress', priority: 'medium' },
  { id: 'T-003', title: 'Weekly safety check', due: '2025-10-18', equipmentId: 'lift-1', assignedTo: 'worker2', status: 'completed', priority: 'low' }
];

const SAMPLE_INVENTORY = [
  { id: 'sp-001', name: 'Brake Pads', qty: 12, minQty: 5 },
  { id: 'sp-002', name: 'Coolant', qty: 2, minQty: 5 }
];

const SAMPLE_NOTIFICATIONS = [
  { id: 'N-1', title: 'New task assigned to you', role: 'worker', message: 'Monthly lift inspection assigned', time: '2025-10-16 10:00' },
  { id: 'N-2', title: 'Low stock: Coolant', role: 'admin', message: 'Coolant below minimum threshold', time: '2025-10-18 08:00' }
];
