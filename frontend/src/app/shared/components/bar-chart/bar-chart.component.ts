import { Component, input } from '@angular/core';

export interface ChartItem {
  label: string;
  total: number;
}

@Component({
  selector: 'app-bar-chart',
  standalone: true,
  imports: [],
  templateUrl: './bar-chart.component.html',
  styleUrl: './bar-chart.component.css',
})
export class BarChartComponent {
  readonly items = input<ChartItem[]>([]);

  max(): number {
    const max = Math.max(1, ...this.items().map((i) => i.total));
    return max;
  }

  width(item: ChartItem): number {
    return (item.total * 100) / this.max();
  }
}