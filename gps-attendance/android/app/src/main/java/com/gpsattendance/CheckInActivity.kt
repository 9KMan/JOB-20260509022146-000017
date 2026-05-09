package com.gpsattendance

import android.Manifest
import android.content.Intent
import android.content.pm.PackageManager
import android.os.Bundle
import android.view.View
import android.widget.ArrayAdapter
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.lifecycle.lifecycleScope
import com.gpsattendance.data.*
import com.gpsattendance.databinding.ActivityCheckInBinding
import kotlinx.coroutines.launch

class CheckInActivity : AppCompatActivity() {
    private lateinit var binding: ActivityCheckInBinding
    private lateinit var locationHelper: LocationHelper
    private var currentLocation: LocationHelper.LocationData? = null
    private var currentAttendanceId: Int? = null
    private var isCheckedIn = false

    private val locationPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestMultiplePermissions()
    ) { permissions ->
        if (permissions[Manifest.permission.ACCESS_FINE_LOCATION] == true) {
            getLocation()
        } else {
            Toast.makeText(this, "Location permission required", Toast.LENGTH_SHORT).show()
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityCheckInBinding.inflate(layoutInflater)
        setContentView(binding.root)
        locationHelper = LocationHelper(this)

        setupUI()
        checkLocationPermission()
        loadPending()
    }

    private fun setupUI() {
        val sites = listOf("Main Office", "Branch Office", "Remote Site")
        val adapter = ArrayAdapter(this, android.R.layout.simple_spinner_dropdown_item, sites)
        binding.spinnerSite.adapter = adapter

        binding.btnCheckIn.setOnClickListener { checkIn() }
        binding.btnCheckOut.setOnClickListener { checkOut() }
        binding.btnHistory.setOnClickListener { startActivity(Intent(this, HistoryActivity::class.java)) }
        binding.btnLogout.setOnClickListener {
            GPSAttendanceApp.instance.prefs.logout()
            startActivity(Intent(this, LoginActivity::class.java))
            finish()
        }

        binding.swSync.isChecked = true
    }

    private fun checkLocationPermission() {
        when {
            ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION)
                == PackageManager.PERMISSION_GRANTED -> getLocation()
            else -> locationPermissionLauncher.launch(arrayOf(
                Manifest.permission.ACCESS_FINE_LOCATION,
                Manifest.permission.ACCESS_COARSE_LOCATION
            ))
        }
    }

    private fun getLocation() {
        binding.tvLocationStatus.text = "Getting location..."
        lifecycleScope.launch {
            currentLocation = locationHelper.getCurrentLocation()
            if (currentLocation != null) {
                binding.tvLocationStatus.text = "Location: ${currentLocation!!.latitude}, ${currentLocation!!.longitude}"
            } else {
                binding.tvLocationStatus.text = "Failed to get location"
            }
        }
    }

    private fun checkIn() {
        val location = currentLocation
        if (location == null) {
            Toast.makeText(this, "Please wait for location", Toast.LENGTH_SHORT).show()
            return
        }

        binding.btnCheckIn.isEnabled = false
        binding.progressBar.visibility = View.VISIBLE

        lifecycleScope.launch {
            try {
                val token = "Bearer ${GPSAttendanceApp.instance.prefs.authToken}"
                val empId = GPSAttendanceApp.instance.prefs.employeeId
                val siteId = 1

                val response = RetrofitClient.api.checkIn(
                    token,
                    CheckInRequest(empId, siteId, location.latitude, location.longitude)
                )
                if (response.success) {
                    currentAttendanceId = response.attendance_id
                    isCheckedIn = true
                    binding.btnCheckIn.visibility = View.GONE
                    binding.btnCheckOut.visibility = View.VISIBLE
                    Toast.makeText(this@CheckInActivity, "Checked in!", Toast.LENGTH_SHORT).show()
                }
            } catch (e: Exception) {
                if (binding.swSync.isChecked) {
                    queueOfflineCheckIn()
                } else {
                    Toast.makeText(this@CheckInActivity, "Check-in failed: ${e.message}", Toast.LENGTH_SHORT).show()
                }
            } finally {
                binding.btnCheckIn.isEnabled = true
                binding.progressBar.visibility = View.GONE
            }
        }
    }

    private fun queueOfflineCheckIn() {
        val location = currentLocation ?: return
        val empId = GPSAttendanceApp.instance.prefs.employeeId
        lifecycleScope.launch {
            GPSAttendanceApp.instance.db.pendingAttendanceDao().insert(
                PendingAttendance(empId, 1, "checkin", location.latitude, location.longitude)
            )
            Toast.makeText(this@CheckInActivity, "Queued for sync", Toast.LENGTH_SHORT).show()
        }
    }

    private fun checkOut() {
        val location = currentLocation
        if (location == null) {
            Toast.makeText(this, "Please wait for location", Toast.LENGTH_SHORT).show()
            return
        }

        val attId = currentAttendanceId
        if (attId == null) {
            Toast.makeText(this, "Not checked in", Toast.LENGTH_SHORT).show()
            return
        }

        binding.btnCheckOut.isEnabled = false
        binding.progressBar.visibility = View.VISIBLE

        lifecycleScope.launch {
            try {
                val token = "Bearer ${GPSAttendanceApp.instance.prefs.authToken}"
                val empId = GPSAttendanceApp.instance.prefs.employeeId

                val response = RetrofitClient.api.checkOut(
                    token,
                    CheckOutRequest(empId, attId, location.latitude, location.longitude)
                )
                if (response.success) {
                    isCheckedIn = false
                    currentAttendanceId = null
                    binding.btnCheckOut.visibility = View.GONE
                    binding.btnCheckIn.visibility = View.VISIBLE
                    Toast.makeText(this@CheckInActivity, "Checked out!", Toast.LENGTH_SHORT).show()
                }
            } catch (e: Exception) {
                Toast.makeText(this@CheckInActivity, "Check-out failed: ${e.message}", Toast.LENGTH_SHORT).show()
            } finally {
                binding.btnCheckOut.isEnabled = true
                binding.progressBar.visibility = View.GONE
            }
        }
    }

    private fun loadPending() {
        lifecycleScope.launch {
            val pending = GPSAttendanceApp.instance.db.pendingAttendanceDao().getAll()
            if (pending.isNotEmpty()) {
                binding.tvPending.text = "${pending.size} pending sync"
                binding.tvPending.visibility = View.VISIBLE
            }
        }
    }
}