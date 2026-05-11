class Employee {
  final String id;
  final String name;
  final String email;
  final String? department;
  final String? designation;

  Employee({
    required this.id,
    required this.name,
    required this.email,
    this.department,
    this.designation,
  });

  factory Employee.fromJson(Map<String, dynamic> json) {
    return Employee(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      department: json['department'],
      designation: json['designation'],
    );
  }
}

class Attendance {
  final String id;
  final String empId;
  final String siteId;
  final DateTime? checkIn;
  final DateTime? checkOut;
  final double? checkInLat;
  final double? checkInLng;
  final double? checkOutLat;
  final double? checkOutLng;
  final bool valid;

  Attendance({
    required this.id,
    required this.empId,
    required this.siteId,
    this.checkIn,
    this.checkOut,
    this.checkInLat,
    this.checkInLng,
    this.checkOutLat,
    this.checkOutLng,
    this.valid = false,
  });

  factory Attendance.fromJson(Map<String, dynamic> json) {
    return Attendance(
      id: json['id'] ?? '',
      empId: json['emp_id'] ?? '',
      siteId: json['site_id'] ?? '',
      checkIn: json['check_in_time'] != null ? DateTime.parse(json['check_in_time']) : null,
      checkOut: json['check_out_time'] != null ? DateTime.parse(json['check_out_time']) : null,
      checkInLat: json['check_in_lat']?.toDouble(),
      checkInLng: json['check_in_lng']?.toDouble(),
      checkOutLat: json['check_out_lat']?.toDouble(),
      checkOutLng: json['check_out_lng']?.toDouble(),
      valid: json['check_in_valid'] ?? false,
    );
  }
}

class Site {
  final String id;
  final String name;
  final String address;
  final double latitude;
  final double longitude;
  final int radiusMeters;

  Site({
    required this.id,
    required this.name,
    required this.address,
    required this.latitude,
    required this.longitude,
    this.radiusMeters = 100,
  });

  factory Site.fromJson(Map<String, dynamic> json) {
    return Site(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      address: json['address'] ?? '',
      latitude: double.tryParse(json['latitude']?.toString() ?? '0') ?? 0,
      longitude: double.tryParse(json['longitude']?.toString() ?? '0') ?? 0,
      radiusMeters: int.tryParse(json['radius_meters']?.toString() ?? '100') ?? 100,
    );
  }
}