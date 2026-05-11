import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:intl/intl.dart';
import 'services/api_service.dart';
import 'models/models.dart';

class HistoryScreen extends StatefulWidget {
  const HistoryScreen({super.key});

  @override
  State<HistoryScreen> createState() => _HistoryScreenState();
}

class _HistoryScreenState extends State<HistoryScreen> {
  List<Attendance> _history = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadHistory();
  }

  Future<void> _loadHistory() async {
    setState(() => _isLoading = true);
    try {
      final prefs = await SharedPreferences.getInstance();
      final empId = prefs.getString('emp_id') ?? '';
      final data = await ApiService.getHistory(empId);
      setState(() {
        _history = (data as List).map((e) => Attendance.fromJson(e)).toList();
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Attendance History'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _error != null
              ? Center(child: Text(_error!))
              : _history.isEmpty
                  ? const Center(child: Text('No records found'))
                  : ListView.builder(
                      itemCount: _history.length,
                      itemBuilder: (context, index) {
                        final record = _history[index];
                        return Card(
                          margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                          child: ListTile(
                            leading: Icon(
                              record.valid ? Icons.check_circle : Icons.cancel,
                              color: record.valid ? Colors.green : Colors.red,
                            ),
                            title: Text(
                              record.checkIn != null
                                  ? DateFormat('MMM d, yyyy').format(record.checkIn!)
                                  : 'Unknown',
                            ),
                            subtitle: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                if (record.checkIn != null)
                                  Text('In: ${DateFormat('HH:mm').format(record.checkIn!)}'),
                                if (record.checkOut != null)
                                  Text('Out: ${DateFormat('HH:mm').format(record.checkOut!)}'),
                              ],
                            ),
                            trailing: record.valid
                                ? const Chip(label: Text('Valid'))
                                : const Chip(label: Text('Invalid'), backgroundColor: Colors.red),
                          ),
                        );
                      },
                    ),
    );
  }
}