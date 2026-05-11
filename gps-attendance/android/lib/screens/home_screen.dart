import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:geolocator/geolocator.dart';
import 'services/location_service.dart';
import 'services/api_service.dart';
import 'package:intl/intl.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  String _empName = '';
  String _empId = '';
  bool _isCheckedIn = false;
  DateTime? _lastCheckIn;
  bool _isLoadingLocation = false;
  Position? _currentPosition;

  @override
  void initState() {
    super.initState();
    _loadEmpData();
  }

  Future<void> _loadEmpData() async {
    final prefs = await SharedPreferences.getInstance();
    setState(() {
      _empId = prefs.getString('emp_id') ?? '';
      _empName = prefs.getString('emp_name') ?? 'Employee';
    });
  }

  Future<void> _captureLocation() async {
    setState(() => _isLoadingLocation = true);
    final position = await LocationService.getCurrentPosition();
    setState(() {
      _currentPosition = position;
      _isLoadingLocation = false;
    });
    if (position == null && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Could not get GPS location')),
      );
    }
  }

  Future<void> _checkIn() async {
    if (_currentPosition == null) {
      await _captureLocation();
      if (_currentPosition == null) return;
    }
    setState(() => _isLoadingLocation = true);
    try {
      final result = await ApiService.checkin(
        _currentPosition!.latitude,
        _currentPosition!.longitude,
        '1',
      );
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(result['message'] ?? 'Check-in ${result['success'] == true ? 'OK' : 'failed'}'),
          ),
        );
        if (result['success'] == true) {
          setState(() {
            _isCheckedIn = true;
            _lastCheckIn = DateTime.now();
          });
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e')),
        );
      }
    } finally {
      setState(() => _isLoadingLocation = false);
    }
  }

  Future<void> _checkOut() async {
    if (_currentPosition == null) await _captureLocation();
    if (_currentPosition == null) return;
    setState(() => _isLoadingLocation = true);
    try {
      final result = await ApiService.checkout(
        _currentPosition!.latitude,
        _currentPosition!.longitude,
      );
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(result['message'] ?? 'Check-out ${result['success'] == true ? 'OK' : 'failed'}')),
        );
        if (result['success'] == true) {
          setState(() => _isCheckedIn = false);
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e')),
        );
      }
    } finally {
      setState(() => _isLoadingLocation = false);
    }
  }

  Future<void> _logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    await prefs.remove('emp_id');
    if (mounted) Navigator.of(context).pushReplacementNamed('/');
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Hello, $_empName'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.history),
            onPressed: () => Navigator.pushNamed(context, '/history'),
          ),
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: _logout,
          ),
        ],
      ),
      body: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            Card(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Column(
                  children: [
                    Icon(
                      _isCheckedIn ? Icons.check_circle : Icons.access_time,
                      size: 64,
                      color: _isCheckedIn ? Colors.green : Colors.grey,
                    ),
                    const SizedBox(height: 16),
                    Text(
                      _isCheckedIn ? 'Checked In' : 'Not Checked In',
                      style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                    ),
                    if (_lastCheckIn != null) ...[
                      const SizedBox(height: 8),
                      Text(
                        'Last: ${DateFormat('MMM d, yyyy HH:mm').format(_lastCheckIn!)}',
                        style: const TextStyle(color: Colors.grey),
                      ),
                    ],
                    if (_currentPosition != null) ...[
                      const SizedBox(height: 8),
                      Text(
                        'GPS: ${_currentPosition!.latitude.toStringAsFixed(6)}, ${_currentPosition!.longitude.toStringAsFixed(6)}',
                        style: const TextStyle(fontSize: 12, color: Colors.grey),
                      ),
                    ],
                  ],
                ),
              ),
            ),
            const SizedBox(height: 24),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: _isLoadingLocation ? null : _captureLocation,
                icon: const Icon(Icons.my_location),
                label: const Text('Get My Location'),
                style: ElevatedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 16),
                ),
              ),
            ),
            const SizedBox(height: 16),
            Row(
              children: [
                Expanded(
                  child: ElevatedButton(
                    onPressed: _isCheckedIn || _isLoadingLocation ? null : _checkIn,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.green,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                    ),
                    child: const Text('CHECK IN'),
                  ),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: ElevatedButton(
                    onPressed: !_isCheckedIn || _isLoadingLocation ? null : _checkOut,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.orange,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                    ),
                    child: const Text('CHECK OUT'),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}