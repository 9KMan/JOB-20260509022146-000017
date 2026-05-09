package com.gpsattendance

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.gpsattendance.data.AttendanceHistoryRecord
import com.gpsattendance.databinding.ItemHistoryBinding

class HistoryAdapter(private val records: List<AttendanceHistoryRecord>) :
    RecyclerView.Adapter<HistoryAdapter.ViewHolder>() {

    class ViewHolder(val binding: ItemHistoryBinding) : RecyclerView.ViewHolder(binding.root)

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        return ViewHolder(ItemHistoryBinding.inflate(LayoutInflater.from(parent.context), parent, false))
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val record = records[position]
        holder.binding.tvDate.text = record.check_in_time.substringBefore(" ")
        holder.binding.tvSite.text = record.site_name
        holder.binding.tvCheckIn.text = record.check_in_time.substringAfter(" ")
        holder.binding.tvCheckOut.text = record.check_out_time?.substringAfter(" ") ?: "-"
        holder.binding.tvStatus.text = if (record.check_out_time != null) "Complete" else "Active"
    }

    override fun getItemCount() = records.size
}