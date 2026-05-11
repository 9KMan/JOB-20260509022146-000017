import 'package:sqflite/sqflite.dart';

class DatabaseService {
  static final DatabaseService instance = DatabaseService._init();
  static Database? _database;

  DatabaseService._init();

  Future<Database> get database async {
    if (_database != null) return _database!;
    _database = await _initDB('attendance.db');
    return _database!;
  }

  Future<Database> _initDB(String filePath) async {
    final dbPath = await getDatabasesPath();
    final path = '$dbPath/$filePath';
    return await openDatabase(path, version: 1, onCreate: _createDB);
  }

  Future<void> _createDB(Database db, int version) async {
    await db.execute('''
      CREATE TABLE attendance_queue (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        emp_id TEXT NOT NULL,
        site_id TEXT NOT NULL,
        action TEXT NOT NULL,
        lat REAL NOT NULL,
        lng REAL NOT NULL,
        timestamp TEXT NOT NULL,
        synced INTEGER DEFAULT 0
      )
    ''');
    await db.execute('''
      CREATE TABLE attendance_cache (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        emp_id TEXT NOT NULL,
        check_in TEXT,
        check_out TEXT,
        valid INTEGER
      )
    ''');
  }

  Future<int> queueAttendance(String empId, String siteId, String action, double lat, double lng) async {
    final db = await database;
    return await db.insert('attendance_queue', {
      'emp_id': empId,
      'site_id': siteId,
      'action': action,
      'lat': lat,
      'lng': lng,
      'timestamp': DateTime.now().toIso8601String(),
      'synced': 0,
    });
  }

  Future<List<Map<String, dynamic>>> getUnsynced() async {
    final db = await database;
    return await db.query('attendance_queue', where: 'synced = 0');
  }

  Future<void> markSynced(int id) async {
    final db = await database;
    await db.update('attendance_queue', {'synced': 1}, where: 'id = ?', whereArgs: [id]);
  }

  Future<void> cacheAttendance(String empId, String? checkIn, String? checkOut, int valid) async {
    final db = await database;
    await db.insert('attendance_cache', {
      'emp_id': empId,
      'check_in': checkIn,
      'check_out': checkOut,
      'valid': valid,
    });
  }

  Future<List<Map<String, dynamic>>> getCachedHistory(String empId) async {
    final db = await database;
    return await db.query('attendance_cache', where: 'emp_id = ?', whereArgs: [empId]);
  }
}