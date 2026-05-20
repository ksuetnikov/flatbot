export interface ChartReadingPoint {
    label: string;
    value: number;
    fullLabel: string;
}

interface ReadingsLineChartProps {
    data: ChartReadingPoint[];
}

const padding = { top: 24, right: 24, bottom: 48, left: 56 };

export function ReadingsLineChart({ data }: ReadingsLineChartProps) {
    if (data.length === 0) {
        return (
            <p className="py-12 text-center text-sm text-muted-foreground">Нет данных для графика</p>
        );
    }

    const width = 720;
    const height = 360;
    const chartW = width - padding.left - padding.right;
    const chartH = height - padding.top - padding.bottom;

    const values = data.map((d) => d.value);
    const minV = Math.min(...values);
    const maxV = Math.max(...values);
    const range = maxV - minV || 1;
    const yMin = minV - range * 0.08;
    const yMax = maxV + range * 0.08;

    const toX = (index: number) =>
        padding.left + (data.length === 1 ? chartW / 2 : (index / (data.length - 1)) * chartW);
    const toY = (value: number) =>
        padding.top + chartH - ((value - yMin) / (yMax - yMin)) * chartH;

    const points = data.map((d, i) => `${toX(i)},${toY(d.value)}`).join(' ');
    const yTicks = 5;

    return (
        <div className="w-full overflow-x-auto">
            <svg
                viewBox={`0 0 ${width} ${height}`}
                className="mx-auto h-auto min-w-[320px] max-w-full"
                role="img"
                aria-label="График показаний"
            >
                {Array.from({ length: yTicks + 1 }).map((_, i) => {
                    const value = yMin + ((yMax - yMin) * (yTicks - i)) / yTicks;
                    const y = toY(value);
                    return (
                        <g key={i}>
                            <line
                                x1={padding.left}
                                y1={y}
                                x2={width - padding.right}
                                y2={y}
                                className="stroke-border"
                                strokeDasharray="4 4"
                            />
                            <text
                                x={padding.left - 8}
                                y={y + 4}
                                textAnchor="end"
                                className="fill-muted-foreground text-[11px]"
                            >
                                {value.toLocaleString('ru-RU', { maximumFractionDigits: 2 })}
                            </text>
                        </g>
                    );
                })}

                <line
                    x1={padding.left}
                    y1={padding.top + chartH}
                    x2={width - padding.right}
                    y2={padding.top + chartH}
                    className="stroke-foreground"
                    strokeWidth={1.5}
                />
                <line
                    x1={padding.left}
                    y1={padding.top}
                    x2={padding.left}
                    y2={padding.top + chartH}
                    className="stroke-foreground"
                    strokeWidth={1.5}
                />

                <polyline
                    points={points}
                    fill="none"
                    className="stroke-primary"
                    strokeWidth={2.5}
                    strokeLinejoin="round"
                    strokeLinecap="round"
                />

                {data.map((d, i) => (
                    <g key={i}>
                        <circle
                            cx={toX(i)}
                            cy={toY(d.value)}
                            r={5}
                            className="fill-primary stroke-background"
                            strokeWidth={2}
                        >
                            <title>{`${d.fullLabel}: ${d.value.toLocaleString('ru-RU')}`}</title>
                        </circle>
                        <text
                            x={toX(i)}
                            y={height - 12}
                            textAnchor="middle"
                            className="fill-muted-foreground text-[10px]"
                        >
                            {d.label}
                        </text>
                    </g>
                ))}
            </svg>
        </div>
    );
}

