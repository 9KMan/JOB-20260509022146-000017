package com.gpsattendance

import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.gpsattendance.data.AttendanceHistoryRecord
import com.gpsattendance.data.GPSAttendanceApp
import com.gpsattendance.data.RetrofitClient
import com.gpsattendance.databinding.ActivityHistoryBinding
import kotlinx.coroutines.launch

class HistoryActivity : AppCompatActivity() {
    private lateinit var binding: ActivityHistoryBinding

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityHistoryBinding.inflate(layoutInflater)
        setContentView(binding.root)

        binding.toolbar.setNavigationOnClickListener { finish() }
        loadHistory()
    }

    private fun loadHistory() {
        binding.progressBar.visibility = View.VISIBLE
        lifecycleScope.launch {
            try {
                val token = "Bearer ${GPSAttendanceApp.instance.prefs.authToken}"
                val empId = GPSAttendanceApp.instance.prefs.employeeId
                val response = RetrofitClient.api.getHistory(token, empId)
                showHistory(response.records)
            } catch (e: Exception) {
                Toast.makeText(this@HistoryActivity, "Failed to load: ${e.message}", Toast.LENGTH_SHORT).show()
            } finally {
                binding.progressBar.visibility = View.GONE
            }
        }
    }

    private fun showHistory(records: List<AttendanceHistoryRecord>) {
        if (records.isEmpty()) {
            binding.tvEmpty.visibility = View.VISIBLE
            return
        }
        binding.recyclerView.layoutManager = LinearLayoutManager(this)
        binding.recyclerView.adapter = HistoryAdapter(records)
    }
}