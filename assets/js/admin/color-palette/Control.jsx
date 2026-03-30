import React, { useCallback, useEffect, useMemo, useState } from "react";
import { __ } from "@wordpress/i18n";
import { AddNewPalette } from "./components/AddNewPalette";
import { Title } from "./components/Title";
import { Description } from "./components/Description";
import { PaletteList } from "./components/PaletteList";

const Control = ({ control, layout }) => {
	const { inputAttrs } = control.params;
	const [values, setValues] = useState({ ...control.setting.get() });

	const palettes = useMemo(() => values.palettes || {}, [values]);

	useEffect(() => {
		control.setting.set(values);
	}, [values, control.setting]);

	const LayoutBasedPalettes = useMemo(() => {
		const result = {};

		for (const [key, palette] of Object.entries(palettes)) {
			if (palette.base === layout) {
				result[key] = palette;
			}
		}

		return result;
	}, [palettes, layout]);

	const handleAddPalette = useCallback(
		(paletteSlug, newPalette) => {
			setValues((prev) => ({
				...prev,
				palettes: {
					...prev.palettes,
					[paletteSlug]: newPalette,
				},
				activePalette: {
					...prev.activePalette,
					[layout]: paletteSlug,
				},
			}));
		},
		[layout]
	);

	return (
		<div {...inputAttrs}>
			<Title />

			<Description />

			<PaletteList
				palettes={LayoutBasedPalettes}
				layout={layout}
				values={values}
				setValues={setValues}
				control={control}
			/>

			<AddNewPalette
				values={values}
				palettes={LayoutBasedPalettes}
				onAddPalette={handleAddPalette}
				activePalette={values.activePalette[layout]}
				disabled={!Object.keys(LayoutBasedPalettes).length}
			/>
		</div>
	);
};

export default Control;
