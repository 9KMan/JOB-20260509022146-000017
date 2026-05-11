import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static const String baseUrl = 'http://YOUR_API_HOST/gps-attendance/api';

  static Future<Map<String, String>> _headers() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('auth_token') ?? '';
    return {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer $token',
    };
  }

  static Future<Map<String, dynamic>> login(String username, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({'username': username, 'password': password}),
    );
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> checkin(double lat, double lng, String siteId) async {
    final headers = await _headers();
    final response = await http.post(
      Uri.parse('$baseUrl/attendance/checkin'),
      headers: headers,
      body: jsonEncode({
        'latitude': lat,
        'longitude': lng,
        'site_id': siteId,
        'check_in_time': DateTime.now().toIso8601String(),
      }),
    );
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> checkout(double lat, double lng) async {
    final headers = await _headers();
    final response = await http.post(
      Uri.parse('$baseUrl/attendance/checkout'),
      headers: headers,
      body: jsonEncode({
        'latitude': lat,
        'longitude': lng,
        'check_out_time': DateTime.now().toIso8601String(),
      }),
    );
    return jsonDecode(response.body);
  }

  static Future<List<dynamic>> getHistory(String empId, {String? from, String? to}) async {
    final headers = await _headers();
    String url = '$baseUrl/attendance/history?emp_id=$empId';
    if (from != null) url += '&from=$from';
    if (to != null) url += '&to=$to';
    final response = await http.get(Uri.parse(url), headers: headers);
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> verifyLocation(double lat, double lng, String siteId) async {
    final headers = await _headers();
    final response = await http.post(
      Uri.parse('$baseUrl/verify-location'),
      headers: headers,
      body: jsonEncode({
        'latitude': lat,
        'longitude': lng,
        'site_id': siteId,
      }),
    );
    return jsonDecode(response.body);
  }
}