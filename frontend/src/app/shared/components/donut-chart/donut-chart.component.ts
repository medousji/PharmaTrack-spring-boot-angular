import { Component, computed, input } from '@angular/core';

import type { ChartItem } from '@shared/components/bar-chart/bar-chart.component';

const RADIUS = 40;
const CIRCUMFERENCE = 2 * Math.PI * RADIUS;

export const CHART_PALETTE = [
  '#1d4ed8',
  '#3b82f6',
  '#0ea5e9',
  '#10b981',
  '#f59e0b',
  '#ef4444',
  '#8b5cf6',
  '#64748b',
];

@Component({
  selector: 'app-donut-chart',
  standalone: true,
  imports: [],
  templateUrl: './donut-chart.component.html',
  styleUrl: './donut-chart.component.css',
})
export class DonutChartComponent {
  readonly items = input<ChartItem[]>([]);
  readonly palette = input<string[]>(CHART_PALETTE);

  readonly total = computed(() => this.items().reduce((sum, i) => sum + i.total, 0));

  readonly segments = computed(() => {
    const total = this.total();
    const palette = this.palette();
    let offset = 0;
    return this.items().map((item, index) => {
      const length = total > 0 ? (item.total / total) * CIRCUMFERENCE : 0;
      const segment = {
        key: item.label,
        color: palette[index % palette.length],
        dasharray: `${length} ${CIRCUMFERENCE - length}`,
        dashoffset: -offset,
      };
      offset += length;
      return segment;
    });
  });
}